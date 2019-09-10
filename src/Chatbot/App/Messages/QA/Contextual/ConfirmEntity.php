<?php


namespace Commune\Chatbot\App\Messages\QA\Contextual;


use Commune\Chatbot\App\Messages\QA\Confirm;
use Commune\Chatbot\App\Messages\QA\QuestionReplyIds;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Session\Session;

class ConfirmEntity extends Confirm
{
    use ContextualTrait;

    const REPLY_ID = QuestionReplyIds::CONFIRM_ENTITY;

   /**
    * @var mixed
    */
   protected $confirmValue;


   public function __construct(
       string $question,
       IntentMessage $intent,
       string $entityName
   )
   {
       $this->init($intent, $entityName);
       $this->confirmValue = $intent->__get($entityName);
       parent::__construct($question);
   }

   protected function matchIntentToAnswer(Session $session, IntentMessage $intent): ? Answer
   {
       $confirmed = $intent->confirmedEntities[$this->intentName] ?? null;

       if (is_null($confirmed)) {
           return null;
       }

       $origin = $this->getOriginIntent($session);
       if (isset($origin)) {
           $confirmedEntities = $origin->confirmedEntities;
           if ($confirmed) {
               $confirmedEntities[$this->entityName] = true;
           } else {
               $confirmedEntities[$this->entityName] = false;
           }
           $origin->confirmedEntities = $confirmedEntities;
           $session->setPossibleIntent($origin);
       }

       $index = intval($confirmed);

       return $this->newAnswer(
           $session->incomingMessage->message,
           $this->suggestions[$index],
           $index
       );
   }

   protected function matchAnswerToIntent(Session $session, Answer $answer): void
   {
       $confirmed = $answer->hasChoice(1);
       $originIntent = $this->getOriginIntent($session);

       if (isset($originIntent)) {
           $confirmedEntities = $originIntent->confirmedEntities;
           $confirmedEntities[$this->entityName] = $confirmed;
           $originIntent->confirmedEntities = $confirmedEntities;
           $session->setPossibleIntent($originIntent);
       }
   }

    /**
     * @return mixed
     */
    public function getConfirmValue()
    {
        return $this->confirmValue;
    }
}