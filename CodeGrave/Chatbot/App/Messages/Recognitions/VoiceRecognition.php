<?php


namespace Commune\Chatbot\App\Messages\Recognitions;


use Commune\Chatbot\App\Messages\Media\Audio;
use Commune\Chatbot\Blueprint\Message\Media\AudioMsg;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\Transformed\RecognitionMsg;
use Commune\Chatbot\Framework\Messages\AbsVerbal;

/**
 * 语音的识别结果.
 */
class VoiceRecognition extends AbsVerbal implements RecognitionMsg
{
    /**
     * @var Audio
     */
    protected $voice;

    public function __construct(AudioMsg $voice, string $recognition)
    {
        $this->voice = $voice;
        parent::__construct($recognition);
    }

    public function __sleep(): array
    {
        return array_merge(parent::__sleep(), ['voice']);
    }

    /**
     * @return Audio
     */
    public function getMedia(): AudioMsg
    {
        return $this->voice;
    }

    public function getOriginMessage(): Message
    {
        return $this->voice;
    }


    public static function mock()
    {
        return new static(Audio::mock(), 'hello');
    }


}