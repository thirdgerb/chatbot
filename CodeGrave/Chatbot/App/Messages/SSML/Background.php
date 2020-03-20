<?php


namespace Commune\Chatbot\App\Messages\SSML;


use Commune\Chatbot\App\Messages\SSML\SayAs\Number;
use Commune\Chatbot\App\Messages\SSML\SayAs\Telephone;
use Commune\Chatbot\Framework\Messages\AbsSSML;

class Background extends AbsSSML
{
    public function __construct(string $content, string $source, bool $repeat = true, array $subSsmls = [])
    {
        parent::__construct($content, ['src' => $source, 'repeat' => $repeat], $subSsmls);
    }


    public function getTag(): string
    {
        return 'background';
    }

    public static function mock()
    {
        return new static('test {{number}} and {{telephone}} ', 'as background music', true, ['number' => Number::mock(), 'telephone' => Telephone::mock()]);
    }

}