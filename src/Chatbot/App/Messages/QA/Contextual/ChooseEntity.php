<?php


namespace Commune\Chatbot\App\Messages\QA\Contextual;


use Commune\Chatbot\App\Messages\QA\Choose;
use Commune\Chatbot\App\Messages\QA\QuestionReplyIds;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Session\Session;

class ChooseEntity extends Choose  implements ContextualQ
{
    use ContextualTrait;

    const REPLY_ID = QuestionReplyIds::CHOOSE_ENTItY;

    public function __construct(
        string $question,
        IntentMessage $intent,
        string $entityName,
        array $options
    )
    {
        $default = $intent->__get($entityName);
        $valueToChoice = array_flip($options);
        $defaultChoice = null;
        if (isset($valueToChoice[$default])) {
            $defaultChoice = $valueToChoice[$default];
        }

        parent::__construct($question, $options, $defaultChoice);
        $this->init($intent, $entityName);
    }

    protected function matchIntentToAnswer(Session $session, IntentMessage $intent): ? Answer
    {
        $value = $intent->__get($this->entityName);
        if (!isset($value)) {
            return null;
        }
        $origin = $this->getOriginIntent($session);

        if (isset($origin)) {
            $origin->__set($this->entityName, $value);
            $session->setPossibleIntent($origin);
        }

        $valueToChoice = array_flip($this->suggestions);
        if (!isset($valueToChoice[$value])) {
            $choice = $valueToChoice[$value];
            return $this->newAnswer(
                $session->incomingMessage->message,
                $value,
                $choice
            );
        }

        return null;
    }

    protected function matchAnswerToIntent(Session $session, Answer $answer): void
    {
        $value = $answer->toResult();
        $origin = $this->getOriginIntent($session);
        if (isset($origin)) {
            $origin->__set($this->entityName, $value);
            $session->setPossibleIntent($origin);
        }
    }


}