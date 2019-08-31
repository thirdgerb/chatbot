<?php


namespace Commune\Chatbot\OOHost\Session;

use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\Config\Host\OOHostConfig;
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
 *
 * ************* dialog api ************
 *
 * @property-read Dialog $dialog
 * @property-read SessionMemory $memory
 *
 * ************* scoping ************
 *
 * @property-read string $sessionId
 *
 * @property-read string $belongsTo  session 是可嵌套的.内层session都从属于一个外层session. session layer could wrap each other like an onion.
 *
 * @property-read Scope $scope   Session目前的作用域.
 *
 *
 * ************* contexts ************
 *
 * @property-read ContextRegistrar $contextRepo
 * @property-read IntentRegistrar $intentRepo
 *
 * ************* components ************
 *
 * @property-read LoggerInterface $logger
 * @property-read Repository $repo
 * @property-read ChatbotConfig $chatbotConfig
 * @property-read OOHostConfig $hostConfig
 *
 *
 */
interface Session extends RunningSpy
{
    // construct method must accept variable "belongsTo"
    const BELONGS_TO_VAR = 'belongsTo';


    /*----- 响应会话 -----*/

    /**
     * hear incoming message and response
     * this api could use in middleware, child session etc.
     *
     * @param Message $message
     * @param Navigator|null $navigator
     */
    public function hear(Message $message, Navigator $navigator = null) : void;

    /**
     * the incoming message has been heard
     * @return bool
     */
    public function isHeard() : bool;

    /**
     * the current session is told to quit
     * @return bool
     */
    public function isQuiting() : bool ;

    /*----- nlu 相关 -----*/

    public function setMatchedIntent(IntentMessage $intent) : void;

    public function getMatchedIntent() : ? IntentMessage;

    /*----- 创建一个director -----*/

    /**
     * goto or create a sub level session.
     *
     * parent session can deliver unhandled message to child session
     * and parent session should handle miss matched message from child session
     *
     * like onion middleware
     *
     * @param string $belongsTo
     * @param \Closure $rootMaker
     * @return Session
     */
    public function newSession(string $belongsTo, \Closure $rootMaker) : Session;

    /*----- 保存必要的数据. -----*/

    public function makeRootContext() : Context;

    /*----- 保存必要的数据. -----*/

    /**
     * 不保存任何数据.
     *
     * keep session status same as last turn, do not record any change
     */
    public function beSneak() : void;

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