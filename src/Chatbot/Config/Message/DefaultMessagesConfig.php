<?php

/**
 * Class DefaultMessage
 * @package Commune\Chatbot\Config\Message
 */

namespace Commune\Chatbot\Config\Message;


use Commune\Support\Option;

/**
 * @property-read string $platformNotAvailable
 * @property-read string $chatIsTooBusy
 * @property-read string $systemError
 * @property-read string $messageMissMatched
 * @property-read string $farewell
 *
 */
class DefaultMessagesConfig extends Option
{
    public static function stub(): array
    {
        return [
            'platformNotAvailable' => 'system.platformNotAvailable',
            'chatIsTooBusy' => 'system.chatIsTooBusy',
            'systemError' => 'system.systemError',
            'farewell' => 'dialog.farewell',
            'messageMissMatched' => 'dialog.missMatched',
        ];
    }


}