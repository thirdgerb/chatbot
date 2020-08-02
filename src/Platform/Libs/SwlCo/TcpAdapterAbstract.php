<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Libs\SwlCo;

use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Platform\Adapter;
use Commune\Support\Utils\TypeUtils;
use Commune\Blueprint\Exceptions\CommuneLogicException;

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

    /**
     * @var string|null
     */
    protected $error = null;

    /**
     * @var AppRequest|null
     */
    protected $request;

    /**
     * TcpAdapterAbstract constructor.
     * @param TcpPacker $packer
     * @param string $appId
     */
    public function __construct(TcpPacker $packer, string $appId)
    {
        $this->packer = $packer;
        $this->appId = $appId;

        $this->init($packer->input());
    }

    abstract protected function getRequestInterface() : string;

    abstract protected function getResponseInterface() : string;

    abstract protected function checkWouldClose(AppResponse $response) : void;

    /**
     * @param string $input
     * @return null|AppRequest
     */
    abstract protected function unserialize(string $input) : ? AppRequest;

    abstract protected function serialize($response) : string;

    protected function init(string $input) : void
    {
        $un = $this->unserialize($input);

        if (isset($this->error)) {
            return;
        }

        $interface = $this->getRequestInterface();

        // 不可反序列化
        if (empty($un)) {
            $this->error = "input is not babel serialized string";

        // 不合法请求
        } elseif (! TypeUtils::isA($un, $interface)) {
            $type = TypeUtils::getType($un);
            $this->error = "input is not $interface protocal, $type given";

            // 合法请求
        } else {
            $this->request = $un;
        }
    }

    public function isInvalidRequest(): ? string
    {
        return $this->error;
    }

    public function getRequest() : AppRequest
    {
        if (!isset($this->request)) {
            throw new CommuneLogicException(
                "invalid adapter should not call getRequest, "
                . $this->error
            );
        }

        return $this->request;
    }

    public function sendResponse(AppResponse $response): void
    {
        $interface = $this->getResponseInterface();
        if (!is_a($response, $interface, true)) {
            $type = TypeUtils::getType($response);
            throw new CommuneLogicException(
                __METHOD__
                . " only accept $interface response, $type given"
            );
        }

        $se = $this->serialize($response);
        $this->packer->output($se);

        $this->checkWouldClose($response);
    }



    public function destroy(): void
    {
        unset(
            $this->packer,
            $this->request
        );
    }

}