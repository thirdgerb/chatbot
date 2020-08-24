<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Command;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Framework\Command\CommandMsg;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $desc
 * @property-read string $signature
 * @property-read callable $handler
 */
class CallableCmdDef extends AContextCmdDef
{

    /**
     * @param string $signature
     * @param string $desc
     * @param array $callable       为防止必然发生的用了闭包导致内存不释放, 干脆禁止数组外的形式.
     * @return CallableCmdDef
     */
    public static function instance(
        string $signature,
        string $desc,
        array $callable
    ) : CallableCmdDef
    {
        if (!is_callable($callable)) {
            throw new InvalidArgumentException(
                "expect callable"
            );
        }

        return new static([
            'desc' => $desc,
            'signature' => $signature,
            'handler' => $callable,
        ]);
    }

    public static function stub(): array
    {
        return [
            'desc' => '',
            'signature' => '',
            'handler' => null,
        ];
    }

    public function handle(
        Dialog $dialog,
        CommandMsg $message
    ): ? Operator
    {
        $handler = $this->handler;
        return $handler($dialog, $message);
    }


}