<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Predefined\SystemInts;

use Commune\Message\Blueprint\Tag\NeverToUser;
use Commune\Message\Constants\SystemIntents;
use Commune\Message\Predefined\IIntentMsg;


/**
 * 系统内部用于交换记忆的消息.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $recollectionName
 * @property-read array $memory
 */
class RecollectionExchangeInt extends IIntentMsg implements NeverToUser
{
    public function __construct(
        string $recollectionName,
        array $memory
    )
    {
        parent::__construct(
            SystemIntents::RECOLLECTION_EXCHANGE,
            [
                'recollectionName' => $recollectionName,
                'memory' => $memory
            ]
        );
    }

}