<?php


namespace Commune\Chatbot\App\Messages\QA\Contextual;


use Commune\Chatbot\App\Messages\QA\Choose;
use Commune\Chatbot\App\Messages\QA\QuestionReplyIds;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\OOHost\Context\Intent\IntentDefinition;
use Commune\Chatbot\OOHost\Session\Session;

class ChooseIntent extends Choose
{
    const REPLY_ID = QuestionReplyIds::CHOOSE_INTENT;

    protected $intents = [];

    public function __construct(string $question, array $options, array $intents, $defaultChoice = null)
    {
        $this->intents = $intents;
        parent::__construct($question, $options, $defaultChoice);
    }

    public function parseAnswer(Session $session): ? Answer
    {
        $intent = $session->getMatchedIntent();
        if (isset($intent)) {
            foreach ($this->intents as $index => $intentName) {
                if ($intent->nameEquals($intentName)) {
                    return $this->answer = $this->newAnswer(
                        $session->incomingMessage->message,
                        $this->suggestions[$index] ?? '',
                        $index
                    );
                }
            }
        }

        // choose intent 不能反向匹配. 命中答案不意味着有正确的 intent 解析.
        return parent::parseAnswer($session);
    }

}