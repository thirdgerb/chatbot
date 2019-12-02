<?php


namespace Commune\Chatbot\OOHost\Context\Contracts;

use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\Intent\IntentDefinition;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;

/**
 * @method IntentDefinition getDef(string $contextName) : ? Definition
 */
interface IntentRegistrar extends ContextRegistrar
{

    /*---------- match ----------*/

    /**
     * 根据有可能存在的intent, 进行匹配.
     *
     * @param NLU $nlu
     * @param Message $message
     * @return IntentMessage|null
     */
    public function matchIntent(NLU $nlu, Message $message) : ? IntentMessage;


    /**
     * 按intent name 进行匹配.
     * 不考虑是否已经存在 matchedIntent
     *
     * @param string $intentName
     * @param NLU $nlu
     * @param Message $message
     * @return IntentMessage|null
     */
    public function matchCertainIntent(
        string $intentName,
        NLU $nlu,
        Message $message
    ) : ? IntentMessage;


    /**
     * intent 可以用命令的方式匹配.
     * @param string $intentName
     * @return bool
     */
    public function isCommandIntent(string $intentName) : bool;
}