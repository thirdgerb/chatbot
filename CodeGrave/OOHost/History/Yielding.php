<?php


namespace Commune\Chatbot\OOHost\History;

use Commune\Chatbot\OOHost\Session\SessionData;
use Commune\Chatbot\OOHost\Session\SessionDataIdentity;

class Yielding implements SessionData
{

    /**
     * @var Thread
     */
    public $thread;

    /**
     * @var string
     */
    public $contextId;

    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
        $this->contextId = $thread->currentTask()->getContextId();
    }

    public function toSessionIdentity(): SessionDataIdentity
    {
        return new SessionDataIdentity(
            $this->getSessionDataId(),
            $this->getSessionDataType()
        );
    }

    public function shouldSave(): bool
    {
        return true;
    }

    public function getSessionDataType(): string
    {
        return SessionData::YIELDING_TYPE;
    }

    public function getSessionDataId(): string
    {
        return $this->contextId;
    }


}