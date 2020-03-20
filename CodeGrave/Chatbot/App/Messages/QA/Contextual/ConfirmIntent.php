<?php


namespace Commune\Chatbot\App\Messages\QA\Contextual;


use Commune\Chatbot\App\Messages\QA\Confirm;
use Commune\Chatbot\App\Messages\ReplyIds;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Context\Intent\PlaceHolderIntent;
use Commune\Chatbot\OOHost\Session\Session;

class ConfirmIntent extends Confirm  implements ContextualQ
{
   use ContextualTrait;

   const REPLY_ID = ReplyIds::CONFIRM_INTENT;

   public function __construct(
       string $question,
       IntentMessage $intent
   )
   {
       $this->initContextual($intent);
       parent::__construct($question);
   }

   protected function matchIntentToAnswer(Session $session, IntentMessage $intent): ? Answer
   {
       $confirmed = $intent->isConfirmed;

       if (is_null($confirmed)) {
           return null;
       }

       $origin = $this->getOriginIntent($session);

       if (isset($origin)) {
           $origin->isConfirmed = $confirmed;
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
           $originIntent->isConfirmed = $confirmed;
           $session->setPossibleIntent($originIntent);
       }
   }


    public static function mock()
    {
        return new ConfirmIntent(
            'ask',
            new PlaceHolderIntent(
                'test',
                ['a' =>1 , 'b'=>2]
            )
        );
    }


}