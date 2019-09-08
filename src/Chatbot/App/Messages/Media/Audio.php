<?php


namespace Commune\Chatbot\App\Messages\Media;


use Commune\Chatbot\Blueprint\Message\Media\AudioMsg;
use Commune\Chatbot\Framework\Messages\AbsMedia;

class Audio extends AbsMedia implements AudioMsg
{
    public function namesAsDependency(): array
    {
        $names = parent::namesAsDependency();
        $names[] = AudioMsg::class;
        return $names;
    }
}