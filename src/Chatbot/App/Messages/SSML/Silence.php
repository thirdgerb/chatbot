<?php


namespace Commune\Chatbot\App\Messages\SSML;


use Commune\Chatbot\Framework\Messages\AbsSSML;

class Silence extends AbsSSML
{

    public function __construct(int $milliseconds)
    {
        parent::__construct('', ['time' => (float)$milliseconds/1000]);
    }

    public function getTag(): string
    {
        return 'silence';
    }


}