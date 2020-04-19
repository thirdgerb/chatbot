<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Auth;

use Commune\Framework\Blueprint\ReqContainer;
use Commune\Framework\Exceptions\InvalidClassException;
use Commune\Ghost\Blueprint\Auth\Authority;
use Commune\Ghost\Blueprint\Auth\Policy;
use Commune\Message\Blueprint\Message;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IAuthority implements Authority
{
    /**
     * @var ReqContainer
     */
    protected $container;

    public function forbid(string $policy, array $payload): ? Message
    {
        if (!is_a($policy, Policy::class, TRUE)) {
            throw new InvalidClassException(
                Policy::class,
                $policy
            );
        }

        // 没有绑定过就算允许.
        if (!$this->container->bound($policy)) {
            return null;
        }

        /**
         * @var Policy $policyIns
         */
        $policyIns = $this->container->make($policy);
        return $policyIns->invoke($payload);
    }

    public function allow(string $policy, array $payload): bool
    {
        $message = $this->forbid($policy, $payload);
        return !isset($message);
    }


}