<?php


namespace Commune\Chatbot\OOHost\NLU;


use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\OOHost\Session\SessionData;
use Commune\Chatbot\OOHost\Session\SessionDataIdentity;

class MissIntentMessage implements SessionData
{
    /**
     * @var IncomingMessage
     */
    protected $msg;

    /**
     * MissIntentMessage constructor.
     * @param IncomingMessage $msg
     */
    public function __construct(IncomingMessage $msg)
    {
        $this->msg = $msg;
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
        return static::class;
    }

    public function getSessionDataId(): string
    {
        return $this->msg->getId();
    }


}