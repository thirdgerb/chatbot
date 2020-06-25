<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\SwooleCo\Supports;

use Commune\Support\Babel\Babel;
use Commune\Support\Utils\TypeUtils;
use Commune\Blueprint\Platform\Adapter;
use Commune\Support\Babel\BabelSerializable;
use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CoTcpGhostBabelAdapter implements Adapter
{

    /**
     * @var CoTcpPacker
     */
    protected $packer;

    /**
     * @var string|null
     */
    protected $error = null;

    /**
     * @var GhostRequest|null
     */
    protected $request;

    /**
     * CoTcpGhostBabelAdapter constructor.
     * @param CoTcpPacker $packer
     */
    public function __construct(CoTcpPacker $packer)
    {
        $this->packer = $packer;
        $this->init($packer->input());
    }


    protected function init(string $input) : void
    {
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
            throw new CommuneLogicException(
                "invalid adapter should not call getRequest, "
                . $this->error
            );
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