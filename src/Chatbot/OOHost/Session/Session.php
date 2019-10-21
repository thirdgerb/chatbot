<?php


namespace Commune\Chatbot\OOHost\Session;

use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Contracts\RootContextRegistrar;
use Commune\Chatbot\OOHost\Context\Contracts\RootIntentRegistrar;
use Commune\Chatbot\OOHost\Context\Contracts\RootMemoryRegistrar;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\Config\Children\OOHostConfig;
use Commune\Chatbot\OOHost\History\Tracker;
use Psr\Log\LoggerInterface;

/**
 *
 * define relative object getter as property not method
 * make session api more clearly
 *
 * ************* incoming ************
 *
 * @property-read IncomingMessage $incomingMessage
 * @property-read Conversation $conversation
 * @property-read NLU $nlu
 *
 * ************* dialog api ************
 *
 * @property-read Dialog $dialog
 * @property-read SessionMemory $memory
 *
 * ************* scoping ************
 *
 * @property-read string $sessionId
 * @property-read Scope $scope   Session目前的作用域.
 *
 *
 * ************* contexts ************
 *
 * @property-read RootContextRegistrar $contextRepo
 * @property-read RootIntentRegistrar $intentRepo
 * @property-read RootMemoryRegistrar $memoryRepo
 *
 * ************* components ************
 *
 * @property-read LoggerInterface $logger
 * @property-read Repository $repo
 * @property-read ChatbotConfig $chatbotConfig
 * @property-read OOHostConfig $hostConfig
 * @property-read Tracker $tracker
 *
 *
 */
interface Session extends RunningSpy
{
    // construct method must accept variable "sessionId"
    const SESSION_ID_VAR = 'sessionId';


    /*----- 响应会话 -----*/

    /**
     * hear incoming message and response
     * this api could use in middleware, child session etc.
     *
     * @param Message $message
     * @param Navigator|null $navigator
     */
    public function handle(Message $message, Navigator $navigator = null) : void;

    /**
     * the incoming message has been heard
     * @return bool
     */
    public function isHandled() : bool;

    /**
     * the current session is told to quit
     * @return bool
     */
    public function isQuiting() : bool ;

    /*----- nlu 相关 -----*/

    /**
     * @param IntentMessage $intent
     */
    public function setMatchedIntent(IntentMessage $intent) : void;

    /**
     * @return IntentMessage|null
     */
    public function getMatchedIntent() : ? IntentMessage;

    /**
     * 主动设置一个可能的 intent
     * @param IntentMessage $intent
     */
    public function setPossibleIntent(IntentMessage $intent) : void;

    /**
     * 尝试根据intent名字, 获取一个intent.
     * 会主动进行匹配.
     *
     * @param string $intentName
     * @return IntentMessage|null
     */
    public function getPossibleIntent(string $intentName) : ? IntentMessage;

    /*----- 保存必要的数据. -----*/

    public function makeRootContext() : Context;

    /*----- 保存必要的数据. -----*/

    /**
     * 要求 session 不保存任何数据.
     * 等于用根 context 响应一次无状态的请求.
     *
     * keep session status same as last turn, do not record any change
     */
    public function beSneak() : void;

    /**
     * session 是否是 sneak 状态.
     * @return bool
     */
    public function isSneaky() : bool;

    /**
     * 需要退出 session
     *
     * request session to quit
     */
    public function shouldQuit() : void;

    /**
     * trigger finish event each turn of conversation
     */
    public function finish() : void;
}