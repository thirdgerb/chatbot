<?php


namespace Commune\Chatbot\App\Components\Predefined\Attitudes;


use Commune\Chatbot\App\Intents\MessageIntent;
use Commune\Chatbot\OOHost\Context\Exiting;

class GreetInt extends MessageIntent
{
    const SIGNATURE = 'greet';

    const DESCRIPTION = '问好';

    // 例句都可以用 nlu example manager 进行修改
    const EXAMPLES = [
        '你好',
    ];

    public static function getContextName(): string
    {
        return 'attitudes.'.static::SIGNATURE;
    }

    public function __exiting(Exiting $listener): void
    {
    }



}