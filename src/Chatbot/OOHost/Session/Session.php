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
 * Session 是多轮对话的根对象. 管理多轮对话所有数据和 api
 * 用属性, 而不是方法的方式来定义这些子对象, 是为了让 session 自身的方法看起来更清晰
 *
 *
 * define relative object getter as property not method
 * make session api more clearly
 *
 * ************* incoming ************
 *
 * @property-read IncomingMessage $incomingMessage 输入的消息
 * @property-read Conversation $conversation 单轮对话的请求级容器
 * @property-read NLU $nlu 保存自然语言相关信息的 nlu 单元
 *
 * ************* dialog api ************
 *
 * @property-read Dialog $dialog 对话管理的 api
 * @property-read SessionMemory $memory 通过配置定义的 memory模块
 *
 * ************* scoping ************
 *
 * @property-read string $sessionId  Session 的 id
 * @property-read Scope $scope   Session目前的作用域.
 *
 *
 * ************* contexts ************
 *
 * @property-read RootContextRegistrar $contextRepo 注册了所有 Context 的仓库
 * @property-read RootIntentRegistrar $intentRepo 注册了所有 Intent 的仓库
 * @property-read RootMemoryRegistrar $memoryRepo 注册了所有 Memory 的仓库
 *
 * ************* components ************
 *
 * @property-read LoggerInterface $logger Session的日志模块
 * @property-read Repository $repo  操作 SessionData 的仓库
 * @property-read ChatbotConfig $chatbotConfig  机器人的配置
 * @property-read OOHostConfig $hostConfig  host 的配置
 * @property-read Tracker $tracker 记录上下文切换的追踪者
 *
 *
 */
interface Session extends RunningSpy
{
    // construct method must accept variable "sessionId"
    const SESSION_ID_VAR = 'sessionId';


    /*----- 响应会话 -----*/

    /**
     * Session 收到一个消息, 并触发所有响应逻辑和状态变更.
     *
     * hear incoming message and response
     * this api could use in middleware, child session etc.
     *
     * @param Message $message  输入消息, 通常是 incomingMessage, 但也可以自定义.
     * @param Navigator|null $navigator   可指定第一个执行的 Navigator, 否则默认是 CallbackStage
     */
    public function handle(Message $message, Navigator $navigator = null) : void;

    /**
     * Handle 方法如果成功运行, 并完成了响应, 则返回 true
     * 如果运行了 Handle 方法, 但无法理解输入信息, 则返回 false
     *
     * the incoming message has been heard
     * @return bool
     */
    public function isHandled() : bool;

    /**
     * 当前 Session 是否得到了退出的指令.
     *
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
     *
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

    /*----- 生成上下文的根节点 -----*/

    public function makeRootContext() : Context;

    /*----- 保存必要的数据. -----*/

    /**
     * 要求 session 只传递回复给 Conversation, 不保存任何状态变更.
     *
     * keep session status same as last turn, do not record any change
     */
    public function beSneak() : void;

    /**
     * 当前 session 是否是 sneak 状态.
     *
     * @return bool
     */
    public function isSneaky() : bool;

    /**
     * 告知 session 需要结束掉当前会话.
     *
     * request session to quit
     */
    public function shouldQuit() : void;

    /**
     * trigger finish event each turn of conversation
     */
    public function finish() : void;
}