<?php

namespace Commune\Chatbot\OOHost\Context;

use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Session\SessionData;
use Commune\Chatbot\OOHost\Session\SessionInstance;
use Commune\Support\Arr\Dictionary;

/**
 * Context 是 chatbot 最核心的概念.
 *
 * 它包含以下方面:
 * 1. 上下文记忆
 * 2. 对多轮对话的定义 ( 多轮对话每一个阶段称之为一个 stage )
 * 3. 对多轮对话每一轮的定义 ( checkpoint )
 * 4. 控制 context 之间的跳转.
 *
 *
 * 运行过程
 *
 * __depend -> __onStart -> _on{stageName}... -> __existing
 *
 */
interface Context extends
    SessionInstance, // context只有在session中才能获取上下文, 必须传入session才能实例化
    SessionData, // context 可以根据 id,  在session 中存储, 读取.
    Message, // context 本身定义为 Message, 可以在 checkpoint 中作为message参数回调
    Dictionary  // context 可以像对象, 或者数组一样操作它存储的数据.
{

    const EXITING_LISTENER = '__exiting';
    // 多轮对话的启动环节.
    const INITIAL_STAGE = 'start';
    const STAGE_METHOD_PREFIX = '__on';
    const DEPENDENCY_BUILDER = '__depend';
    // 中间件方法.
    const HEARING_MIDDLEWARE_METHOD = '__hearing';
    const STAGE_MIDDLEWARE_METHOD = '__staging';

    /**
     * Context 的id, 用于存储和读取一个 context 对象.
     * @return string
     */
    public function getId() : string;

    /**
     * 语境的ID. 格式是 '单词.单词.单词'
     * 用类名来表示的话, 反斜杠 '\' 会替换为 '.'.
     * @return string
     */
    public function getName() : string;

    public static function __depend(Depending $depending) : void;

    public function __onStart(Stage $stage): Navigator;

    public function __exiting(Exiting $listener) : void;

    /*--------- value ---------*/

    public function hasAttribute(string $name);

    public function getAttribute(string $name);

    public function setAttribute(string $name, $value) : void;

    public function toAttributes() : array;

    /*--------- contextual ---------*/

    /**
     * 获取 context 的定义类
     * @return Definition
     */
    public function getDef() : Definition;

    /**
     * 当前context 所有的entity是否都有值了.
     * 当 context is prepared, 则可以正式运行 __onStart
     *
     * @return bool
     */
    public function isPrepared() : bool;

    /**
     * 当前依赖的entity
     * @return Entity
     */
    public function depending() : ? Entity;

    /**
     * 依赖的所有Entity
     * @return Entity[]
     */
    public function depends() : array;
}