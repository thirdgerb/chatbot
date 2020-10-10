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

use Commune\Protocols\HostMsg\Convo\QA\Step;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property int|null $current
 * @property int $max
 */
class IStep extends IAnswerMsg implements Step
{

    public static function stub(): array
    {
        return [
            'answer' => '',
            'choice' => null,
            'route' => null,
            'current' => null,
            'max' => 0,
        ];
    }

    public function getStep() : int
    {
        return $this->current;
    }

    public function isMaxStep(): bool
    {
        return $this->current >= $this->max;
    }


}