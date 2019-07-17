<?php


namespace Commune\Chatbot\App\Components\Predefined\Attitudes;


use Commune\Chatbot\App\Intents\MessageIntent;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Emotion\Emotions\Negative;

class DontInt extends MessageIntent implements Negative
{

    const SIGNATURE = 'dont';

    const DESCRIPTION = '别这么做';

    // 例句都可以用 nlu example manager 进行修改
    const EXAMPLES = [
        '不用了',
    ];

    const REGEX = [
        ['/^(不用|不要|不好|别|不用了)$/'],
    ];

    public static function getContextName(): string
    {
        return 'attitudes.'.static::SIGNATURE;
    }

    public function __exiting(Exiting $listener): void
    {
    }



}