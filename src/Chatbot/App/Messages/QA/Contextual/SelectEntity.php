<?php


namespace Commune\Chatbot\App\Messages\QA\Contextual;


use Commune\Chatbot\App\Messages\ReplyIds;
use Commune\Chatbot\App\Messages\QA\Selection;
use Commune\Chatbot\App\Messages\QA\Selects;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Context\Intent\PlaceHolderIntent;
use Commune\Chatbot\OOHost\Session\Session;

class SelectEntity extends Selects implements ContextualQ
{
    use ContextualTrait;

    const REPLY_ID = ReplyIds::SELECT_ENTITY;


    public function __construct(
        string $question,
        IntentMessage $intent,
        string $entityName,
        array $suggestions
    )
    {
        $defaultValues = $intent->__get($entityName);
        $defaultChoices = [];
        if (!empty($defaultValues)) {
            $valueToChoice = array_flip($suggestions);
            foreach ($defaultValues as $value ) {
                if (isset($valueToChoice[$value])) {
                    $defaultChoices[] = $valueToChoice[$value];
                }
            }
        }
        $this->initContextual($intent, $entityName);
        parent::__construct($question, $suggestions, $defaultChoices);
    }

    protected function matchIntentToAnswer(Session $session, IntentMessage $intent): ? Answer
    {
        $values = $intent->__get($this->entityName);
        if (is_array($values)) {

            $valueToChoices = $this->suggestions;

            $answers = [];
            $choices = [];
            foreach ($values as $value) {
                if (isset($valueToChoices[$value])) {
                    $answers[] = $value;
                    $choices[] = $valueToChoices[$value];
                }
            }

            if (!empty($answers)) {
                $origin = $this->getOriginIntent($session);
                if (isset($origin)) {
                    $origin->__set($this->entityName, $answers);
                    $session->setPossibleIntent($origin);
                }

                return new Selection(
                    $session->incomingMessage->message,
                    $answers,
                    $choices
                );
            }
        }
        return null;
    }

    /**
     * @param Session $session
     * @param Selection $answer
     */
    protected function matchAnswerToIntent(Session $session, Answer $answer): void
    {
        $results = $answer->getResults();
        $intent = $this->getOriginIntent($session);
        if (isset($intent) && !empty($results) && is_array($results)) {
            $intent->__set($this->entityName, $results);
            $session->setPossibleIntent($intent);
        }
    }

    public static function mock()
    {
        return new SelectEntity(
            'ask',
            new PlaceHolderIntent(
                'test',
                ['a' => 1]
            ),
            'b',
            [1, 2, 3, 4]
        );
    }

}