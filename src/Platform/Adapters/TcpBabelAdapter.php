<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Adapters;

use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Blueprint\Platform\Adapter;
use Commune\Platform\Packers\TcpPacker;
use Commune\Support\Babel\Babel;
use Commune\Support\Babel\BabelSerializable;
use Commune\Support\Utils\TypeUtils;


/**
 * 基于 Tcp 通道, 用 Babel 模块进行序列化后传输的 Adapter.
 *
 * 作为示范的 Demo, 没有设计任何安全协议.
 * 假设 TcpPacker 传来的就是一个 Babel::serialize 后的字符串.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TcpBabelAdapter implements Adapter
{

    /**
     * @var TcpPacker
     */
    protected $packer;

    /**
     * @var null|string
     */
    protected $error = null;

    /**
     * @var GhostRequest|null;
     */
    protected $request;

    /**
     * TcpBabelAdapter constructor.
     * @param TcpPacker $packer
     */
    public function __construct(TcpPacker $packer)
    {
        $this->packer = $packer;
    }

    protected function init()
    {
        $input = $this->packer->input();
        $un = Babel::unserialize($input);

        // 不可反序列化
        if (empty($un)) {
            $this->error = "input is not babel serialized string";

        // 不合法请求
        } elseif (!$un instanceof GhostRequest) {
            $type = TypeUtils::getType($un);
            $this->error = "input is not GhostRequest Protocal, $type given";

        // 合法请求
        } else {
            $this->request = $un;
        }
    }

    public function isInvalid(): ? string
    {
        return $this->error;
    }

    public function getRequest() : GhostRequest
    {
        if (!isset($this->request)) {
            throw new CommuneLogicException($this->error);
        }

        return $this->request;
    }

    public function sendResponse($response): void
    {
        if (!$response instanceof GhostResponse) {
            $type = TypeUtils::getType($response);
            throw new CommuneLogicException(
                __METHOD__
                . ' only accept '
                . GhostResponse::class
                . ', '
                . $type
                . ' given'
            );
        }

        if (!$response instanceof BabelSerializable) {
            $type = TypeUtils::getType($response);
            throw new CommuneLogicException(
                __METHOD__
                . ' only accept '
                . BabelSerializable::class
                . ' ghost response, '
                . $type
                . ' given'
            );

        }

        $se = Babel::serialize($response);
        $this->packer->output($se);
    }

    public function destroy(): void
    {
        unset(
            $this->packer,
            $this->request
        );
    }


}