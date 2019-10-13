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
        return 'speak';
    }

    public static function mock()
    {
        return new static('test {{background}} {{sub}}', [
            'background' => Background::mock(),
            'sub' => Sub::mock(),
        ]);
    }
}