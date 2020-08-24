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

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\MindDef\Intent\IntentExample;
use Commune\Protocals\HostMsg\IntentMsg;

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
 * 3. alias : 允许用别名来匹配意图.
 * 4. Regex : 正则匹配来获取.
 * 5. Entity : 如果命中了某个指定的 Entity
 * 6. Keyword : 命中了某些关键字.
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
    /**
     * @param Cloner $cloner
     * @return bool
     */
    public function match(Cloner $cloner) : bool;

    /**
     * @param Cloner $cloner
     * @return IntentMsg
     */
    public function toIntentMessage(Cloner $cloner) : IntentMsg;

    /**
     * @return string
     */
    public function getIntentName() : string;

    /**
     * @return string[]
     */
    public function getEntityNames() : array;

    /**
     * @param string[][] $entities  entity 的特征是可能同时命中多个值. 但对于语义而言, 要区分是列表还是非列表.
     *
     * @return array
     */
    public function parseEntities(array $entities) : array;

    /**
     * 语料
     * @return string[]
     */
    public function getExamples() : array;

    /**
     * @param string $example
     */
    public function appendExample(string $example) : void;

    /**
     * 封装后的语料对象
     * @return IntentExample[]
     */
    public function getExampleObjects() : array;

    /**
     * @return array
     */
    public function getKeywords() : array;

    public function appendKeywords(array $words) : void;

    /**
     * @return string[]
     */
    public function getRegex() : array;

    /**
     * 意图所代表的情绪
     * @return string[]
     */
    public function getEmotions() : array;

}