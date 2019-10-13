<?php


namespace Commune\Chatbot\App\Messages\Media;


use Commune\Chatbot\Blueprint\Message\Media\AudioMsg;
use Commune\Chatbot\Framework\Messages\AbsMedia;

class Audio extends AbsMedia implements AudioMsg
{
    public static function mock()
    {
        return new static('audio source');
    }
}