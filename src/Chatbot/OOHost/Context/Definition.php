<?php


namespace Commune\Chatbot\OOHost\Context;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * CommuneChatbot 用面向对象的思维定义了多轮对话, 并使用PHP语言为之建模.
 *
 * 这相当于用面向对象语言PHP, 简单模拟了一个面向对象语言, 用它来定义多轮对话.
 * 与编程语言最大区别在于, 多轮对话的数据不是存在内存中, 而是分布式服务器中.
 *
 * 每一个 Context 类都可以用来定义一种上下文.
 * Definition 对象本质上类似 ReflectionClass, 是对多轮对话对象的抽象.
 *
 */
interface Definition
{
    // existing event
    const FULFILL = 0;
    const REJECTION = 1;
    const CANCEL = 2;
    const FAILURE = 3;
    const BACKWARD = 4;
    const QUIT = 5;

    // predefined tags
    const TAG_MANAGER = 'manager';  // 管理工具
    const TAG_CONFIGURE = 'configure'; // 通过配置生成的intent

    /**
     * 使用construct 参数创建一个 context 对象.
     *
     * create a context
     *
     * @param array $args
     * @return AbsContext
     */
    public function newContext(...$args) : Context;

    /*--------- getter ---------*/

    /**
     * context 类型的名称.
     *
     * context type name
     *
     * @return string
     */
    public function getName() : string;

    /**
     * 上下文 具体实例使用的 php 类
     * class of context instance
     *
     * @return string
     */
    public function getClazz() : string;

    /**
     * 上下文 类的描述
     *
     * description of the context type
     *
     * @return string
     */
    public function getDesc() : string;

    /**
     * 获取context 的类型定义.
     * @return string[]
     */
    public function getTags() : array;


    ###########  entity ###########
    #
    # context 可以定义 entities 作为一种特殊的属性.
    # 所有 entity 属性是必要的, 只有 entity 数据存在, 才会正式进入 start stage
    # 通过 entity 可以快速定义 context 的必填参数和调用流程. 不依赖 stage
    #
    ###############################

    /**
     * @param Entity $entity
     */
    public function addEntity(Entity $entity) : void;

    public function hasEntity(string $entityName) : bool;

    public function getEntity(string $entityName) : ? Entity;

    /**
     * 获取所有的entity
     * @return Entity[]
     */
    public function getEntities() : array;

    /**
     * @return string[]
     */
    public function getEntityNames() : array;

    /**
     * 所有依赖中的entity
     *
     * @param Context $instance
     * @return Entity[]
     */
    public function dependsEntities(Context $instance) : array;

    /**
     * 当前正在依赖的entity
     *
     * current depending entity
     *
     * @param Context $instance
     * @return Entity|null
     */
    public function dependingEntity(Context $instance) : ? Entity;

    ########### stage ###########
    #
    #   多轮对话上下文存在分形几何式的嵌套关系.
    #   可将之拆分成若干层级的单元
    #
    #   在 CommuneChatbot 中定义了四种级别的单元, 分别是:
    #
    #   - Process : 整个会话只有一个
    #   - thread : 有上下文依赖关系的一组任务
    #   - context : 一个独立的任务单元
    #   - stage : 任务下的一个环节.
    #
    #
    #############################

    /**
     * @param string $stageName
     * @return bool
     */
    public function hasStage(string $stageName) : bool ;

    /**
     * @param string $stage
     * @param callable $builder
     */
    public function setStage(string $stage, callable $builder): void;

    /**
     * @return string[]
     */
    public function getStageNames() : array;


    /*--------- call ---------*/

    /**
     * 运行一个已经实例化的 stage
     *
     * @param string $stage
     * @param Stage $stageRoute
     * @return Navigator
     */
    public function callStage(
        string $stage,
        Stage $stageRoute
    ) : Navigator;

    /**
     * 启动一个stage
     * 会先检查Entity
     *
     * @param Context $self
     * @param Dialog $dialog
     * @param string $stage
     * @return Navigator
     */
    public function startStage(
        Context $self,
        Dialog $dialog,
        string $stage
    ) : Navigator;

    /**
     * 回调一个stage
     *
     * @param Context $self
     * @param Dialog $dialog
     * @param string $stage
     * @param Message|null $callbackValue
     * @return Navigator
     */
    public function callbackStage(
        Context $self,
        Dialog $dialog,
        string $stage,
        Message $callbackValue = null
    ) : Navigator;

    /**
     * 尝试退出一个 context
     *
     * @param int $exiting
     * @param Context $self
     * @param Dialog $dialog
     * @param Context|null $callback
     * @return Navigator|null
     */
    public function callExiting(
        int $exiting,
        Context $self,
        Dialog $dialog,
        Context $callback = null
    ) : ? Navigator;

}