<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Abstracted;

use Commune\Protocals\Abstracted\Choice;
use Commune\Support\Message\AbsMessage;


/**
 * 将输入消息理解成为一种选择
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property mixed|null  $choice       选项 (index)
 * @property string  $answer           答案
 */
class IChoice extends AbsMessage implements Choice
{
    public static function stub(): array
    {
        return [
            'choice' => null,
            'answer' => ''
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function isEmpty(): bool
    {
        return !isset($this->_data['choice'])
            && empty($this->_data['answer']);
    }

    public function getChoice()
    {
        return $this->choice;
    }

    public function hasChoice($choice): bool
    {
        return $this->choice == $choice;
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    public function addChoice($choice, string $answer): void
    {
        $this->choice = $choice;
        $this->answer = $answer;
    }


}