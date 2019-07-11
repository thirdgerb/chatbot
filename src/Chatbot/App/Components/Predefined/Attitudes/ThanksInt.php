<?php


namespace Commune\Chatbot\App\Components\Predefined\Attitudes;


use Commune\Chatbot\App\Intents\MessageIntent;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Emotion\Emotions\Positive;

class ThanksInt extends MessageIntent implements Positive
{

    const SIGNATURE = 'thanks';

    const DESCRIPTION = '感谢';

    // 例句都可以用 nlu example manager 进行修改
    const EXAMPLES = [
        'thanks',
        'thank you',
        '谢谢您',
        '感谢您',
    ];

    public static function getContextName(): string
    {
        return 'attitudes.'.static::SIGNATURE;
    }

    public function __exiting(Exiting $listener): void
    {
    }



}