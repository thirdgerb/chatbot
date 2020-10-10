<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Shell\Tcp;

use Commune\Blueprint\Kernel\Protocols\AppRequest;
use Commune\Blueprint\Kernel\Protocols\AppResponse;
use Commune\Blueprint\Kernel\Protocols\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocols\ShellOutputResponse;
use Commune\Kernel\Protocols\IShellInputRequest;
use Commune\Message\Host\Convo\IText;
use Commune\Message\Intercom\IInputMsg;
use Commune\Platform\Libs\Parser\AppResponseParser;
use Commune\Platform\Libs\SwlCo\TcpAdapterAbstract;

/**
 * 作为示范用的 Adapter
 * 输入输出就是纯字符串.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SwlCoTextShellAdapter extends TcpAdapterAbstract
{
    protected $shouldClose = false;

    protected function getRequestInterface(): string
    {
        return ShellInputRequest::class;
    }

    protected function getResponseInterface(): string
    {
        return ShellOutputResponse::class;
    }

    protected function unserialize(string $input) : ? AppRequest
    {
        $input = trim($input);
        $message = IText::instance($input);

        $socket = $this->packer->conn->exportSocket();
        $info = $socket->getpeername();

        if (!is_array($info) || empty($info)) {
            $this->error =  'invalid socket connection peer name';
            return null;
        }

        $sessionId = md5($this->appId . ':' . json_encode($info));

        $inputMsg = IInputMsg::instance(
            $message,
            $sessionId,
            $creatorId = $sessionId
        );

        $request = IShellInputRequest::instance(
            false,
            $inputMsg
        );

        return $request;
    }

    /**
     * @param ShellOutputResponse $response
     * @return string
     */
    protected function serialize($response): string
    {
        return AppResponseParser::outputsToString($response);
    }

    protected function checkWouldClose(AppResponse $response): void
    {
        if ($this->shouldClose) {
            $this->packer->conn->close();
        }
    }


}