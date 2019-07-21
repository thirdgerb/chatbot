<?php


namespace Commune\Chatbot\App\Messages\Recognitions;


use Commune\Chatbot\App\Messages\Media\Voice;
use Commune\Chatbot\Blueprint\Message\MediaMsg;
use Commune\Chatbot\Blueprint\Message\RecognitionMsg;
use Commune\Chatbot\Framework\Messages\Verbose;

class VoiceRecognition extends Verbose implements RecognitionMsg
{
    protected $voice;

    public function __construct(Voice $voice, string $recognition)
    {
        $this->voice = $voice;
        parent::__construct($recognition);
    }

    /**
     * @return Voice
     */
    public function getMedia(): MediaMsg
    {
        return $this->voice;
    }


}