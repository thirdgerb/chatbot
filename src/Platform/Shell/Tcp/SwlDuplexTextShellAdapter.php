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

use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Message\Host\Convo\IEventMsg;
use Commune\Platform\Libs\SwlAsync\TcpAdapterAbstract;
use Commune\Message\Host\Convo\IText;
use Commune\Message\Intercom\IInputMsg;
use Commune\Platform\Libs\Parser\AppResponseParser;
use Commune\Support\Utils\StringUtils;
use Commune\Kernel\Protocals\IShellInputRequest;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SwlDuplexTextShellAdapter extends TcpAdapterAbstract
{

    protected function isValidRequest(AppRequest $request): bool
    {
        return $request instanceof ShellInputRequest;
    }

    protected function isValidResponse(AppResponse $response): bool
    {
        return $response instanceof ShellOutputResponse;
    }

    protected function serializeResponse(AppResponse $response): string
    {
        return AppResponseParser::outputsToString($response);
    }


    protected function unserializeRequest() : ? AppRequest
    {
        $clientInfo = $this->packer->server->getClientInfo($this->packer->fd);

        $ip = $clientInfo['remote_ip'] ?? '';
        if (empty($ip)) {
            $this->invalid = "invalid ip info $ip";
            return null;
        }

        $creatorName = $ip;
        $creatorId = md5($this->appId . ':'. $creatorName);
        $sessionId = $creatorId;

        // 必须 trim
        $data = StringUtils::trim($this->packer->data);

        $message = IText::instance($data);

        $input = IInputMsg::instance(
            $message,
            $sessionId,
            $creatorId,
            $creatorName
        );

        return IShellInputRequest::instance(
            false,
            $input
        );
    }


}