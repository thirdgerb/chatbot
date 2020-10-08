<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost;

use Commune\Blueprint\Ghost\Cloner\ClonerInstanceStub;
use Commune\Blueprint\Ghost\Context\Dependable;
use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\Exceptions\DefNotDefinedException;
use Commune\Blueprint\Ghost\Exceptions\InvalidQueryException;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;


/**
 * 把核心方法从 Ucl 中拆出来, 当成独立文档.
 *
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $contextName
 * @property-read string $stageName
 * @property-read string[] $query
 */
interface UclInterface extends
    ArrayAndJsonAble,
    Dependable,     // ucl 可以做为 $dialog->dependOn 的参数
    ClonerInstanceStub // ucl 可以作为 context 的属性.
{

    /*------ create ------*/

    /**
     * 生成一个 Ucl 对象
     * @param string $contextName
     * @param array $query
     * @param string $stageName
     * @return Ucl
     */
    public static function make(
        string $contextName,
        array $query = [],
        string $stageName = ''
    ) : Ucl;


    /**
     * 将一个字符串解析成为一个 Ucl 对象.
     * @param string|Ucl $string
     * @return Ucl
     * @throws InvalidArgumentException
     */
    public static function decode($string) : Ucl;

    /**
     * 将 Ucl 对象转化为字符串.
     * @return string
     */
    public function encode() : string;

    /*------ property ------*/

    /**
     * 当前 Ucl 定位的对话, 其唯一ID
     * @return string
     */
    public function getContextId() : string;


    /**
     * 获取当前 Stage 的全名, 也可以生成兄弟 stage 的全名
     * @param string|null $stage
     * @return string
     */
    public function getStageFullname(string $stage = null) : string;

    /*------ compare ------*/

    /**
     * 两个 Ucl 是否在同一个 Context 内, 但不一定是相同的 Context 实例.
     * @param Ucl $ucl
     * @return bool
     */
    public function atSameContext(Ucl $ucl) : bool;

    /**
     * 判断两个 Ucl 是否指的是同一个 Context 实例.
     * 相同实例, stage 可能不一样.
     * @param Ucl $ucl
     * @return bool
     */
    public function isSameContext(Ucl $ucl) : bool;

    /**
     * 判断两个 Ucl 是否完全一致
     * @param string $ucl
     * @return bool
     */
    public function equals($ucl) : bool;

    /*------ redirect ------*/

    /**
     * 从一个 Ucl 走向相同 Context 下的另一个 ucl
     * @param string $stageName
     * @return Ucl
     * @throws InvalidArgumentException
     */
    public function goStage(string $stageName) : Ucl;

    /**
     * 使用 stage 全名来生成相同 Context 下的另一个 Ucl.
     * 关键在于传递 query, contextId 等参数.
     * @param string $fullname
     * @return Ucl
     */
    public function goStageByFullname(string $fullname) : Ucl;

    /*------ validate ------*/

    /**
     * 判断 Ucl 的格式是否正确
     * @return bool
     */
    public function isValidPattern() : bool;

    /**
     * 判断 Ucl 是否正确, 除了格式之外, 还要考虑定义的 Context 都已存在.
     * @param Cloner $cloner
     * @return bool
     */
    public function isValid(Cloner $cloner) : bool;

    /**
     * 检查当前 Ucl 是否合法
     * @param Cloner $cloner
     * @return null|string
     */
    public function isInvalid(Cloner $cloner) : ? string;

    /*------ mindset ------*/

    /**
     * 判断当前 Ucl 对应的 Stage 是否定义过了.
     * @param Cloner $cloner
     * @return bool
     */
    public function stageExists(Cloner $cloner) : bool;

    /**
     * 用 Ucl 获取 StageDef
     * @param Cloner $cloner
     * @return StageDef
     * @throws DefNotDefinedException
     */
    public function findStageDef(Cloner $cloner) : StageDef;

    /**
     * 用 Ucl 获取 ContextDef
     * @param Cloner $cloner
     * @return ContextDef
     * @throws DefNotDefinedException
     * @throws InvalidQueryException
     */
    public function findContextDef(Cloner $cloner) : ContextDef;

    /**
     * 用 Ucl 寻找 intentDef, 可能不存在.
     * 现在的策略是, Stage 如果没有定义过意图匹配相关的逻辑, 就不存储一个 IntentDef
     * @param Cloner $cloner
     * @return IntentDef|null
     */
    public function findIntentDef(Cloner $cloner) : ? IntentDef;

    /**
     * 通过 Ucl 在上下文中查找或生成 Context 实例.
     * @param Cloner $cloner
     * @return Context
     * @throws DefNotDefinedException
     * @throws InvalidArgumentException
     */
    public function findContext(Cloner $cloner) : Context;

    /*------ string ------*/

    /**
     * @return string
     */
    public function __toString() : string;
}
