<?php

namespace Commune\Chatbot\OOHost\Context;

use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Session\SessionData;
use Commune\Chatbot\OOHost\Session\SessionInstance;
use Commune\Support\Arr\Dictionary;
use Commune\Support\Utils\StringUtils;

/**
 * Context 是 chatbot 最核心的概念.
 *
 * 它包含以下方面:
 * 1. 上下文记忆 ( 包括属性 )
 * 2. 对多轮对话的定义 ( 各种方法 )
 * 3. 对多轮对话每一轮的定义 ( stage )
 * 4. 控制 context 之间的跳转 ( redirect ) .
 *
 *
 * 运行过程
 *
 * __depend -> __onStart -> _on{stageName}... -> __existing
 *
 *
 * 注意!!
 * 如果 property 是数组, 直接进行数组操作是无效的, 会触发:
 * Indirect modification of overloaded property
 * 需要先建立临时变量, 赋值, 然后赋值回去才行.
 *
 */
interface Context extends
    SessionInstance, // context只有在session中才能获取上下文, 必须传入session才能实例化
    SessionData, // context 可以根据 id,  在session 中存储, 读取.
    Message, // context 本身定义为 Message, 可以在 checkpoint 中作为message参数回调
    Dictionary  // context 可以像对象, 或者数组一样操作它存储的数据.
{

    // 定义多轮对话每个阶段的方法名前缀.
    // 例如 start 阶段, 命名为 __onStart
    const STAGE_METHOD_PREFIX = '__on';

    // 允许用 annotation 来标注 stage.
    // 在方法的注解上标记  @stage
    // 这两种方法未来更倾向于前者, 因为容错更好.
    const STAGE_ANNOTATION = 'stage';


    // 系统默认的方法.
    // 语境脱出时调用的事件方法名
    const EXITING_LISTENER = '__exiting';

    // 定义多轮对话的依赖属性. 属性本身会通过多轮对话来完善, 全完善后才会进入start阶段.
    const DEPENDENCY_BUILDER = '__depend';

    // 中间件方法.

    // 如果此方法存在, 所有 $dialog->hear(Message $message) 时都会调用此方法,
    // 入参是 Hearing 类.
    // 可以给所有的hearing 定义一些公共的流程.
    const HEARING_MIDDLEWARE_METHOD = '__hearing';

    // 如果此方法存在, 所有stage 方法构建时都会调用它.
    // 入参是 Stage, 返回值是 void
    // 可以给所有的stage 定义公共流程.
    const STAGE_MIDDLEWARE_METHOD = '__staging';

    // 如果此方法存在, 调用 Dialog::hear(), 匹配不到别的意图时
    // 会调用 Hearing::onHelp([$context, '__help']) 方法;
    const CONTEXT_HELP_METHOD = '__help';

    /**
     * 用 tag 可以为context 在registrar 里归类.
     * tag 的类型在 Definition 里有定义.
     * @see Definition
     */
    const CONTEXT_TAGS = [];

    // 多轮对话的启动阶段.
    const INITIAL_STAGE = 'start';

    /**
     * Context 的id, 用于存储和读取一个 context 对象.
     * @return string
     */
    public function getId() : string;

    /**
     * 语境的ID. 格式是 '单词.单词.单词'
     * 用类名来表示的话, 反斜杠 '\' 会替换为 '.'.
     *
     * 合法的字符只有 小写字母, 数字, . 和 _
     * @see StringUtils  StringUtils::validateDefName()
     * @return string
     */
    public function getName() : string;

    /**
     * intent name 可能会有别名, 比如类名, 用这个方法来省略代码.
     * @param string $name
     * @return bool
     */
    public function nameEquals(string $name) : bool;

    /**
     * 定义 Context 依赖的 Entity, 可以是参数, 也可以是另一个 Context
     * @param Depending $depending
     */
    public static function __depend(Depending $depending) : void;

    /**
     * 可以在这里捕获 Context 的逃离事件, 例如 onCancel , onQuit
     * @param Exiting $listener
     */
    public function __exiting(Exiting $listener) : void;

    /**
     * Context 的初始 stage
     * @param Stage $stage
     * @return Navigator
     */
    public function __onStart(Stage $stage): Navigator;

//    /**
//     * 这个方法可以定义一个 Context 内部所有 hearing 的共用方法.
//     * @param Hearing $hearing
//     */
//    public function __hearing(Hearing $hearing) : void;

//    /**
//     * 这个方法可以定义一个 Context 内部所有 Stage 的共用方法.
//     * @param Stage $stage
//     */
//    public function __staging(Stage $stage) : void;

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
     * 当前context 所有的 entity 是否都有值了.
     * 当 context is prepared, 才会正式运行 __onStart
     * 否则会运行 entity 自己的赋值 stage
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


    /*--------- gc环节 ---------*/

    /**
     * 计数器增加
     */
    public function _gc_increment_count() : void;

    /**
     * 计数器减少.
     */
    public function _gc_decrement_count() : void;

    /**
     * 计数器的值
     * @return int
     */
    public function _gc_count() : int;


}