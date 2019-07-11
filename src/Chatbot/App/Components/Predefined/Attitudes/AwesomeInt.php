<?php


namespace Commune\Chatbot\App\Components\Predefined\Attitudes;


use Commune\Chatbot\App\Intents\MessageIntent;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Emotion\Emotions\Positive;

class AwesomeInt extends MessageIntent implements Positive
{

    const SIGNATURE = 'awesome';

    const DESCRIPTION = '厉害';

    // 例句都可以用 nlu example manager 进行修改
    const EXAMPLES = [
        'awesome',
        'wonderful',
        'excellent',
        'very good',
        '牛逼',
        '真厉害',
        '好厉害',
        '太棒了',
        '太赞了',
    ];

    public static function getContextName(): string
    {
        return 'attitudes.'.static::SIGNATURE;
    }

    public function __exiting(Exiting $listener): void
    {
    }



}