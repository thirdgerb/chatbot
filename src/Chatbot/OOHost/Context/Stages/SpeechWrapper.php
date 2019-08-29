<?php


namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\Blueprint\Conversation\Speech;

class SpeechWrapper
{
    /**
     * @var Speech
     */
    protected $speech;

    /**
     * @var bool
     */
    protected $isStart;

    /**
     * MonologWrapper constructor.
     * @param Speech $speech
     * @param bool $isStart
     */
    public function __construct(Speech $speech, bool $isStart)
    {
        $this->speech = $speech;
        $this->isStart = $isStart;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed|null
     */
    public function __call($name, $arguments)
    {
        if ($name === 'trans' || $this->isStart) {
            return call_user_func_array([$this->speech, $name], $arguments);
        }
        return null;
    }

}