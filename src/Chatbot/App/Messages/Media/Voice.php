<?php


namespace Commune\Chatbot\App\Messages\Media;


use Commune\Chatbot\Framework\Messages\AbsMedia;

class Voice extends AbsMedia
{
    public function getText(): string
    {
        return $this->toJson();
    }

    public function toMessageData(): array
    {
        return [
            'mediaId' => $this->mediaId
        ];
    }


}