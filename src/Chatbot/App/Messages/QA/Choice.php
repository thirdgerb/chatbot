<?php


namespace Commune\Chatbot\App\Messages\QA;

use Commune\Chatbot\App\Messages\Text;

class Choice extends VbAnswer
{
    public static function mock()
    {
        return new Choice(new Text('a'), 'a', 0);
    }
}