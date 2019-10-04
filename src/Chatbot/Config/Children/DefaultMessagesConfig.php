<?php

/**
 * Class DefaultMessage
 * @package Commune\Chatbot\Config\Children
 */

namespace Commune\Chatbot\Config\Children;


use Commune\Support\Option;

/**
 * @property-read string $platformNotAvailable
 * @property-read string $chatIsTooBusy
 * @property-read string $systemError
 * @property-read string $unsupported
 * @property-read string $messageMissMatched
 * @property-read string $farewell
 * @property-read string $yes
 * @property-read string $no
 *
 */
class DefaultMessagesConfig extends Option
{
    public static function stub(): array
    {
        return [
            'platformNotAvailable' => 'system.platformNotAvailable',
            'chatIsTooBusy' => 'system.chatIsTooBusy',
            'unsupported' => 'system.unsupported',
            'systemError' => 'system.systemError',
            'farewell' => 'dialog.farewell',
            'messageMissMatched' => 'dialog.missMatched',
            'yes' => 'ask.yes',
            'no' => 'ask.no',
        ];
    }


}