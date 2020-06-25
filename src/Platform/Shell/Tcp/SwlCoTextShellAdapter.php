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

use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Framework\Log\IConsoleLogger;
use Commune\Kernel\Protocals\IShellInputRequest;
use Commune\Message\Host\Convo\IText;
use Commune\Message\Intercom\IInputMsg;
use Commune\Platform\Libs\SwlCo\TcpAdapterAbstract;
use Commune\Protocals\HostMsg\DefaultIntents;
use Commune\Protocals\HostMsg\IntentMsg;
use Psr\Log\LogLevel;

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

    protected function unserialize(string $input)
    {
        $input = trim($input);
        $message = IText::instance($input);

        $socket = $this->packer->conn->exportSocket();
        $info = $socket->getpeername();

        if (!is_array($info) || empty($info)) {
            return 'invalid socket connection peer name';
        }

        $sessionId = md5(json_encode($info));

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
        $outputStr = '';
        $code = $response->getErrcode();
        if ($code !== AppResponse::SUCCESS) {
            $errmsg = $response->getErrmsg();
            $outputStr .= IConsoleLogger::wrapMessage(
                LogLevel::CRITICAL,
                "request failed, code $code, msg $errmsg \n\n"
            );
        }

        $outputs = $response->getOutputs();

        foreach ($outputs as $output) {
            $message = $output->getMessage();
            $text = $message->getText();
            $level = $message->getLevel();

            $outputStr .= IConsoleLogger::wrapMessage(
                $level,
                $text
            );

            $outputStr .= "\n\n";

            if ($message instanceof IntentMsg
                && $message->getProtocalId() === DefaultIntents::SYSTEM_SESSION_QUIT
            ) {
                $this->shouldClose = true;
            }
        }

        return $outputStr;
    }

    protected function checkWouldClose(AppResponse $response): void
    {
        if ($this->shouldClose) {
            var_dump('quit');
            $this->packer->conn->close();
        }
    }


}