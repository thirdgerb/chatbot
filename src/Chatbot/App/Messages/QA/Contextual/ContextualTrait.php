<?php


namespace Commune\Chatbot\App\Messages\QA\Contextual;


use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionDataIdentity;

trait ContextualTrait
{

    /**
     * @var SessionDataIdentity
     */
    protected $intentDataId;

    /**
     * @var string|null
     */
    protected $entityName;

    /**
     * @var string
     */
    protected $intentName;

    /**
     * @var IntentMessage
     */
    protected $intent;

    protected function init(
        IntentMessage $intent,
        string $entityName = null
    )
    {
        $this->intent = $intent;
        $this->entityName = $entityName;
        $this->intentName = $intent->getName();
        $this->intentDataId = $intent->toSessionIdentity();
    }

    /**
     * @return IntentMessage
     */
    public function getIntent(): ? IntentMessage
    {
        return $this->intent;
    }

    public function parseAnswer(Session $session): ? Answer
    {
        $intent = $session->getMatchedIntent();

        // 命中意图.
        if (isset($intent) && $intent->nameEquals($this->intentName)) {
            $answer = $this->matchIntentToAnswer($session, $intent);

            if (isset($answer)) {
                return $this->answer = $answer;
            }
        }

        // 正常匹配结果.
        $this->answer = parent::parseAnswer($session);

        if (!isset($this->answer)) {
            return null;
        }

        $this->matchAnswerToIntent($session, $this->answer);
        return $this->answer;
    }

    protected function getOriginIntent(Session $session) : ? IntentMessage
    {
        $intent = $session->repo->fetchSessionData($this->intentDataId);
        return $intent instanceof IntentMessage ? $intent : null;
    }

    abstract protected function matchIntentToAnswer(Session $session, IntentMessage $intent) : ? Answer;

    abstract protected function matchAnswerToIntent(Session $session, Answer $answer) : void;

    /**
     * @return SessionDataIdentity
     */
    public function getIntentDataId(): SessionDataIdentity
    {
        return $this->intentDataId;
    }

    /**
     * @return string|null
     */
    public function getEntityName(): ? string
    {
        return $this->entityName;
    }

    /**
     * @return string
     */
    public function getIntentName(): string
    {
        return $this->intentName;
    }

    public function __sleep()
    {
        $props = parent::__sleep();
        return array_merge($props, ['intentDataId', 'intentName', 'entityName']);
    }


}