<?php
/**
 * Created by PhpStorm.
 * User: BrightRed
 * Date: 2019/4/14
 * Time: 6:02 PM
 */

namespace Commune\Chatbot\App\ChatPipe\NLP;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Intent\NatureLangUnit;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\Framework\Pipeline\ChatbotPipeImpl;

/**
 * 自然语言识别单元
 * 理想的情况下, 获取的可能的Intent会添加到incomingMessage
 * 然后由 context 里由stageRoute 去匹配.
 *
 * Class NatureLanguagePipe
 * @package Commune\Chatbot\Host\Pipeline
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class NatureLanguagePipe extends ChatbotPipeImpl
{
    /**
     * @var NatureLangUnit
     */
    public $nlu;

    /**
     * NatureLanguagePipe constructor.
     * @param NatureLangUnit $nlu
     */
    public function __construct(NatureLangUnit $nlu)
    {
        $this->nlu = $nlu;
    }

    public function handleUserMessage(Conversation $conversation, \Closure $next): Conversation
    {
        $incomingMessage = $conversation->getRequest()->getIncomingMessage();
        $message = $incomingMessage
            ->getMessage();

        // 只有文本才需要检查语义.
        if (!$message instanceof VerboseMsg) {
            return $next($conversation);
        }

        $intents = $this->nlu->match($incomingMessage);

        // 将预定义的intent 填充进去.
        if (!empty($intents)) {
            foreach ($intents as $intentName => $entities) {
                $incomingMessage->addPossibleIntent($intentName, $entities);
            }
        }

        /**
         * @var Conversation $conversation
         */
        $conversation =  $next($conversation);

        $incomingMessage =  $conversation
            ->getRequest()
            ->getIncomingMessage();

        $matched = $incomingMessage->getMatchedIntent();

        if (isset($matched)) {
            $this->nlu->logMatched(
                $incomingMessage,
                $matched
            );
        } else {
            $this->nlu->logMissMatched(
                $incomingMessage
            );

        }
        return $conversation;
    }

    public function onUserMessageFinally(Conversation $conversation): void
    {
    }


}