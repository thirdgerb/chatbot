<?php


namespace Commune\Chatbot\OOHost\Context;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

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
     * create a context
     * @param array $args
     * @return AbsContext
     */
    public function newContext(...$args) : Context;

    /*--------- getter ---------*/

    public function getName() : string;

    public function getClazz() : string;

    public function getDesc() : string;

    /**
     * 获取context 的类型定义.
     * @return string[]
     */
    public function getTags() : array;

    /*--------- entity ---------*/


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
     * @param Context $instance
     * @return Entity[]
     */
    public function depends(Context $instance) : array;

    /**
     * 当前正在依赖的entity
     * @param Context $instance
     * @return Entity|null
     */
    public function depending(Context $instance) : ? Entity;

    /*--------- stage ---------*/

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
     * @param string $stage
     * @param Stage $stageRoute
     * @return Navigator
     */
    public function callStage(
        string $stage,
        Stage $stageRoute
    ) : Navigator;

    /**
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
     * @param int $exiting
     * @param Context $self
     * @param Dialog $dialog
     * @param Context|null $callback
     * @return Navigator|null
     */
    public function onExiting(
        int $exiting,
        Context $self,
        Dialog $dialog,
        Context $callback = null
    ) : ? Navigator;

}