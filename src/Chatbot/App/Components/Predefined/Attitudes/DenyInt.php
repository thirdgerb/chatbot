<?php


namespace Commune\Chatbot\App\Components\Predefined\Attitudes;


use Commune\Chatbot\App\Intents\MessageIntent;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Emotion\Emotions\Negative;

class DenyInt extends MessageIntent implements Negative
{
    const SIGNATURE = 'deny';

    const DESCRIPTION = '否认';

    // 例句都可以用 nlu example manager 进行修改
    const EXAMPLES = [
        'no',
        'nope',
        'negative',
        'not ok',
        '否认',
        '否定',
        '不是',
        '不对',
        '错了',
        '并非如此',
    ];

    public static function getContextName(): string
    {
        return 'attitudes.'.static::SIGNATURE;
    }

    public function __exiting(Exiting $listener): void
    {
    }



}