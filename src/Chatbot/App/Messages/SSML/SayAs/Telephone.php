<?php


namespace Commune\Chatbot\App\Messages\SSML\SayAs;


use Commune\Chatbot\Framework\Messages\AbsSSML;

class Telephone extends AbsSSML
{

    public function __construct($num)
    {
        parent::__construct($num, ['type' => 'telephone']);
    }

    public function getTag(): string
    {
        return 'say-as';
    }


}