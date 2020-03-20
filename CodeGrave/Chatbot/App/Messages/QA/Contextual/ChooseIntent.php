<?php


namespace Commune\Chatbot\App\Messages\QA\Contextual;


use Commune\Chatbot\App\Messages\QA\Choose;
use Commune\Chatbot\App\Messages\ReplyIds;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Components\Predefined\Intents\Attitudes\AffirmInt;
use Commune\Components\Predefined\Intents\Attitudes\DenyInt;

class ChooseIntent extends Choose
{
    const REPLY_ID = ReplyIds::CHOOSE_INTENT;

    /**
     * @var  string[] 可选的意图名称.
     */
    protected $intents = [];

    public function __construct(string $question, array $options, array $intents, $defaultChoice = null)
    {
        $this->intents = $intents;
        parent::__construct($question, $options, $defaultChoice);
    }

    public function __sleep(): array
    {
        return array_merge(parent::__sleep(), ['intents']);
    }

    public function parseAnswer(Session $session, Message $message = null): ? Answer
    {
        $message = $message ?? $session->incomingMessage->message;

        $intent = $session->getMatchedIntent();
        if (isset($intent)) {
            foreach ($this->intents as $index => $intentName) {
                if ($intent->nameEquals($intentName)) {
                    return $this->answer = $this->newAnswer(
                        $message,
                        $this->suggestions[$index] ?? '',
                        $index
                    );
                }
            }
        }

        // choose intent 不能反向匹配. 命中答案不意味着有正确的 intent 解析.
        return parent::parseAnswer($session, $message);
    }

    /**
     * @return array
     */
    public function getIntents(): array
    {
        return $this->intents;
    }


    public static function mock()
    {
        return new ChooseIntent(
            'ask',
            [
                'y',
                'n',
            ],
            [
                AffirmInt::getContextName(),
                DenyInt::getContextName(),

            ],
            1
        );
    }

}