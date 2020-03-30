<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Prototype\Abstracted;

use Commune\Message\Blueprint\Abstracted\Choice;
use Commune\Support\Arr\ArrayAbleToJson;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IChoice implements Choice
{
    use ArrayAbleToJson;

    public $choices = [];

    public function toArray(): array
    {
        return [
            'choices' => $this->choices
        ];
    }

    public function countChoices(): int
    {
        return count($this->choices);
    }

    public function addChoice($index, string $answer): void
    {
        $this->choices[$index] = $answer;
    }

    public function getAnswers(): array
    {
        return array_values($this->choices);
    }

    public function getChoices(): array
    {
        return array_keys($this->choices);
    }

    public function hasIndex($index, bool $only = false): bool
    {
        return array_key_exists($index, $this->choices)
            && ($only && $this->countChoices() === 1);
    }

    public function hasAnswer(string $answer, bool $only = false): bool
    {
        return in_array($answer, $this->choices)
            && ($only && $this->countChoices() === 1);
    }


}