<?php


namespace Commune\Chatbot\App\Messages\SSML;


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


}