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
class ACallableCmdDef extends AContextCmdDef
{

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