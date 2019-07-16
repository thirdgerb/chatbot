<?php


namespace Commune\Chatbot\App\Components\Predefined\Attitudes;


use Commune\Chatbot\App\Intents\MessageIntent;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Emotion\Emotions\Negative;

class WrongInt extends MessageIntent implements Negative
{
    const SIGNATURE = 'wrong';

    const DESCRIPTION = '错误';

    // 例句都可以用 nlu example manager 进行修改
    const EXAMPLES = [
        '错了',
    ];

    public static function getContextName(): string
    {
        return 'attitudes.'.static::SIGNATURE;
    }
    public function __exiting(Exiting $listener): void
    {
    }


}