<?php


namespace Commune\Chatbot\App\Messages\QA\Contextual;


use Commune\Chatbot\App\Messages\ReplyIds;
use Commune\Chatbot\App\Messages\QA\VbQuestion;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Context\Intent\PlaceHolderIntent;
use Commune\Chatbot\OOHost\Session\Session;

/**
 * 通过 intent 返回的 entity 来生成回答的结果.
 */
class AskEntity extends VbQuestion implements ContextualQ
{
    use ContextualTrait;

    const REPLY_ID = ReplyIds::ASK_ENTITY;

    public function __construct(
        string $question,
        IntentMessage $intent,
        string $entityName,
        $default
    )
    {
        $this->initContextual($intent, $entityName);
        $default = $intent->__get($this->intentName) ?? $default;
        parent::__construct($question, [], null, $default);
    }

    protected function matchIntentToAnswer(Session $session, IntentMessage $intent): ? Answer
    {
        $value = $intent->__get($this->entityName);
        $selfIntent = $this->getOriginIntent($session);

        // 用存储的 intent 去替换掉当前值.
        if (isset($selfIntent)) {
            $selfIntent->__set($this->entityName, $value);
            $session->setPossibleIntent($selfIntent);
        }

        if (isset($value) && is_scalar($value)) {
            return $this->answer = $this->newAnswer(
                $session->incomingMessage->message,
                strval($value),
                null
            );
        }

        return null;
    }

    protected function matchAnswerToIntent(Session $session, Answer $answer): void
    {
        $intent = $this->getOriginIntent($session);
        if (isset($intent)) {
            $intent->__set($this->entityName, $answer->toResult());
            $session->setPossibleIntent($intent);
        }
    }

    public static function mock()
    {
        return new AskEntity(
            'ask',
            new PlaceHolderIntent(
                'test',
                ['a' =>1 , 'b'=>2]
            ),
            'c',
            1
        );
    }

}