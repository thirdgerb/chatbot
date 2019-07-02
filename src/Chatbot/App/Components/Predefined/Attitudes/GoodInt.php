<?php


namespace Commune\Chatbot\App\Components\Predefined\Attitudes;


use Commune\Chatbot\App\Intents\MessageIntent;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Emotion\Emotions\Positive;

class GoodInt extends MessageIntent implements Positive
{

    const SIGNATURE = 'ok';

    const DESCRIPTION = '可以';

    // 例句都可以用 nlu example manager 进行修改
    const EXAMPLES = [
        'ok',
        'okay',
        'good',
        'very good',
        'excellent',
        '好啊',
        '太好了',
        '非常好',
        '不错',
        '真棒',
        '挺好的呀',
        '就这样吧',
        '这样就行',
    ];

    public static function getContextName(): string
    {
        return 'attitudes.'.static::SIGNATURE;
    }

    public function __exiting(Exiting $listener): void
    {
    }



}