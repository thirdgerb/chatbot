<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Host\Convo\QA;

use Commune\Protocols\HostMsg\Convo\QA\Confirmation;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read bool|null $positive
 */
class IConfirmation extends IAnswerMsg implements Confirmation
{
    public static function stub(): array
    {
        return [
            'answer' => '',
            'choice' => null,
            'route' => null,
            'positive' => null,
        ];
    }


    public function isPositive(): bool
    {
        return $this->positive === true;
    }

    public function isNegative(): bool
    {
        return $this->positive === false;
    }


}