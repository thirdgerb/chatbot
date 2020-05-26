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

use Commune\Blueprint\Ghost\MindMeta\Option\ParamOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
interface ParamDef
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
     * @return ParamOption
     */
    public function getOption() : ParamOption;

}
