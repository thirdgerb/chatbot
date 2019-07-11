<?php


namespace Commune\Chatbot\App\Components\Predefined\Attitudes;


use Commune\Chatbot\App\Intents\MessageIntent;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Emotion\Emotions\Positive;

class GoodInt extends MessageIntent implements Positive
{

    const SIGNATURE = 'good';

    const DESCRIPTION = '可以';

    // 例句都可以用 nlu example manager 进行修改
    const EXAMPLES = [
        'ok',
        'okay',
        'good',
        '好啊',
        '不错',
        '也行',
        '挺好的',
        '这样就行',
        '就这样吧',
    ];

    public static function getContextName(): string
    {
        return 'attitudes.'.static::SIGNATURE;
    }

    public function __exiting(Exiting $listener): void
    {
    }



}