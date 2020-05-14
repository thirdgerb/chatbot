<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\MindDef;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
interface DefParam
{
    /**
     * 字段名
     * @return string
     */
    public function getName() : string;

    /**
     * 字段的介绍, 或者向用户要求输入时给出的问题.
     * @return string
     */
    public function getQuery() : string;

    /**
     * @return callable|null
     */
    public function getTypeValidator() : ? callable ;

    /**
     * @return callable|null
     */
    public function getValParser() : ? callable ;


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
     * 作为单轮对话. 只有 Entity 类型才可以作为一个单轮对话.
     * @return StageDef
     */
    public function asStage() : StageDef;
}
