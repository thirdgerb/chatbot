<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Libs\SwlAsync;

use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Blueprint\Platform\Adapter;
use Commune\Kernel\Protocals\IShellInputRequestRequest;
use Commune\Kernel\Protocals\LogContext;
use Commune\Message\Host\Convo\IText;
use Commune\Message\Intercom\IInputMsg;
use Commune\Platform\Libs\Parser\AppResponseParser;
use Commune\Support\Utils\TypeUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class TcpAdapterAbstract implements Adapter
{

    /**
     * @var TcpPacker
     */
    protected $packer;

    /**
     * @var string
     */
    protected $appId;

    /*---- cached ----*/

    /**
     * @var AppRequest
     */
    protected $request;

    /**
     * @var null|string
     */
    protected $invalid = null;

    /**
     * SwlAsyncTextAdapter constructor.
     * @param TcpPacker $packer
     * @param string $appId
     * @param AppRequest $request
     */
    public function __construct(
        TcpPacker $packer,
        string $appId,
        AppRequest $request = null
    )
    {
        $this->packer = $packer;
        $this->appId = $appId;

        $this->request = $request ?? $this->initRequest();

    }

    abstract protected function isValidRequest(AppRequest $request) : bool;

    abstract protected function isValidResponse(AppResponse $response) : bool;

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

        $message = IText::instance(trim($this->packer->data));

        $input = IInputMsg::instance(
            $message,
            $sessionId,
            $creatorId,
            $creatorName
        );

        return IShellInputRequestRequest::instance(
            false,
            $input
        );
    }


    protected function initRequest() : ? AppRequest
    {
        if (isset($this->request)) {
            return $this->request;
        }

        $request = $this->unserializeRequest();

        if (!$this->isValidRequest($request)) {
            $type = TypeUtils::getType($request);
            $this->invalid = "invalid request, expect $interface, $type given";
            return null;
        }

        $sessionId = $request->getSessionId();
        $fd = $this->packer->fd;

        // 设置好路由.
        $this->packer
            ->platform
            ->setSessionRoute($sessionId, $fd);

        return $this->request = $request;

    }

    public function isInvalid(): ? string
    {
        return $this->invalid;
    }


    public function getRequest(): AppRequest
    {
        if (!isset($this->request)) {
            throw new CommuneLogicException(
                "should not query request from adapter when adapter is invalid"
            );
        }
        return $this->request;
    }


    public function sendResponse(AppResponse $response): void
    {

        // 无法发送时, 关闭路由.
        $sessionId = $response->getSessionId();

        if (!$this->packer->exists()) {

            $this->packer->platform->unsetSessionRoute($sessionId);

            // 记录日志.
            $this->packer
                ->platform
                ->getLogger()
                ->warning(
                    __METHOD__
                    . " connection not available",
                    LogContext::responseToContext($response)
                );
            return;
        }

        if (!$this->isValidResponse($response)) {
            $type = TypeUtils::getType($response);
            $this->packer->fail("invalid response, $type given");
            return;
        }

        $output = $this->serializeResponse($response);

        $this->send($sessionId, $output);
    }

    protected function send(string $sessionId, string $data) : void
    {
        $fd = $this->packer->fd;

        $server = $this->packer->server;

        if ($server->exists($fd)) {
            $server->send($fd, $data);
        } else {
            $this->packer->platform->unsetSessionRoute($sessionId);
        }
    }

    public function destroy(): void
    {
        unset(
            $this->packer,
            $this->request
        );
    }


}