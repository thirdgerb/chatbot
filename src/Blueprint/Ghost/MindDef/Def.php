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

use Commune\Blueprint\Ghost\MindMeta\DefMeta;
use Commune\Support\Option\Wrapper;

/**
 * 某种逻辑配置的定义. 可以通过 Meta 数据生成出来.
 * 系统对 Meta 数据的加载, 决定了自己会有哪些逻辑.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 * @method DefMeta getMeta() : Meta
 */
interface Def extends Wrapper
{
    /**
     * Def 名称
     * @return string
     */
    public function getName() : string;

    /**
     * 文字标题
     * @return string
     */
    public function getTitle() : string;

    /**
     * 详细介绍.
     * @return string
     */
    public function getDescription() : string;

}