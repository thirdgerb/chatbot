<?php


namespace Commune\Chatbot\App\Messages\SSML;


use Commune\Chatbot\Framework\Messages\AbsSSML;

class Speak extends AbsSSML
{
    public function __construct(string $content, $subSsmls = [])
    {
        parent::__construct($content, [], $subSsmls);
    }

    public function getTag(): string
    {
        return '';
    }

}