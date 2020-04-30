<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Definition;

use Commune\Blueprint\Ghost\Cloner;

/**
 * 关于意图的定义.
 * 每一个意图都直接关联到一个 Stage, 有的是 Context 的 initial Stage.
 *
 * 所有意图都允许多种匹配方式.
 *
 * 常见以下几种:
 *
 * 1. Intent : 直接用 Comprehension 的 intent 模块去匹配. 最合理的做法.
 * 2. 命令行 : 允许用命令行来开启一个多轮对话
 * 3. Suggestions : 作为上下文的多种选项之一, 允许被选择
 * 4. Regex : 正则匹配来获取.
 * 5. Entity : 如果命中了某个指定的 Entity
 * 6. Keyword : 命中了某些关键字.
 * 7. SoundLike : 尝试匹配表达式的发音.
 *
 * Intent 在上下文中的匹配又分为两种, 精确匹配和模糊匹配.
 *
 * 如果是上下文指定的精确匹配对象, 则使用 IntentDef 进行精确的匹配.
 * 如果是上下文指定的模糊匹配对象(使用了通配符 * ), 则只用 Comprehension->intent 来进行匹配.
 *
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface IntentDef extends Def
{
    public function validate(Cloner $cloner) : bool;


    public function getEntityNames() : array;

    public function asCommand() : string;

    public function asSpell() : string;
}