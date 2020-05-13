<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Mind\Definitions;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
interface ContextParameter
{
    /**
     * 字段名
     * @return string
     */
    public function getName() : string;

    /**
     * @return callable
     */
    public function getTypeValidator() : callable ;

    /**
     * @return bool
     */
    public function isQuery() : bool;

    /**
     * 会长期保存的变量. 否则是只在 Session 生命周期中保存的变量.
     * @return bool
     */
    public function isLongTerm() : bool;

    public function isEntity() : bool;

    /**
     * 是否是数组
     * @return bool
     */
    public function isList() : bool;

    /**
     * 默认值
     * @return mixed|null
     */
    public function getDefault();

    /**
     * @param $value
     * @return mixed
     */
    public function parseSetVal($value);

    /**
     * 作为单轮对话. 只有 Entity 类型才可以作为一个单轮对话.
     * @return StageDef|null
     */
    public function asStage() : ? StageDef;
}
