<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Prototype\SystemInts;

use Commune\Message\Blueprint\Tag\NeverToUser;
use Commune\Message\Constants\SystemIntents;
use Commune\Message\Prototype\IIntentMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $recollectionName
 */
class RecollectionQueryInt extends IIntentMsg implements NeverToUser
{
    public function __construct(
        string $recollectionName
    )
    {
        parent::__construct(
            SystemIntents::RECOLLECTION_QUERY,
            [
                'recollectionName' => $recollectionName,
            ]
        );
    }

    public function toExchange(array $memory) : RecollectionExchangeInt
    {
        return new RecollectionExchangeInt(
            $this->recollectionName,
            $memory
        );
    }
}