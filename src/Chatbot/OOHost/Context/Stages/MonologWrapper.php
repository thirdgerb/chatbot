<?php


namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\Blueprint\Conversation\Monologue;

class MonologWrapper
{
    /**
     * @var Monologue
     */
    protected $monolog;

    /**
     * @var bool
     */
    protected $isStart;

    /**
     * MonologWrapper constructor.
     * @param Monologue $monolog
     * @param bool $isStart
     */
    public function __construct(Monologue $monolog, bool $isStart)
    {
        $this->monolog = $monolog;
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
            return call_user_func_array([$this->monolog, $name], $arguments);
        }
        return null;
    }

}