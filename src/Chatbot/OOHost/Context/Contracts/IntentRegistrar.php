<?php


namespace Commune\Chatbot\OOHost\Context\Contracts;

use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\Intent\IntentDefinition;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Session\Session;

/**
 * @method IntentDefinition getDef(string $contextName) : ? Definition
 */
interface IntentRegistrar extends ContextRegistrar
{

    /*---------- match ----------*/

    /**
     * 根据有可能存在的intent, 进行匹配.
     *
     * @param Session $session
     * @return IntentMessage|null
     */
    public function matchIntent(Session $session) : ? IntentMessage;

    /**
     * 按intent name 进行匹配.
     * @param string $intentName
     * @param Session $session
     * @return IntentMessage|null
     */
    public function matchCertainIntent(string $intentName, Session $session) : ? IntentMessage;


    /**
     * intent 可以用命令的方式匹配.
     * @param string $intentName
     * @return bool
     */
    public function isCommandIntent(string $intentName) : bool;
}