<?php


namespace Commune\Chatbot\App\Components\Predefined\Attitudes;


use Commune\Chatbot\App\Intents\MessageIntent;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Emotion\Emotions\Negative;

class Dont extends MessageIntent implements Negative
{

    const SIGNATURE = 'dont';

    const DESCRIPTION = '别这么做';

    // 例句都可以用 nlu example manager 进行修改
    const EXAMPLES = [
        'no',
        "don't do this",
        '不要啊',
        '不好',
        '别这样',
        '别这么做',
        '这么做是错的',
        '错了',
        '这样不对',
        '不用了',
        '不需要了',
    ];

    public static function getContextName(): string
    {
        return 'attitudes.'.static::SIGNATURE;
    }

    public function __exiting(Exiting $listener): void
    {
    }



}