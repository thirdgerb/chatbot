<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Runtime;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $contextId         当前节点所属语境 id
 * @property-read string $contextName       当前节点所属的语境名称
 * @property-read int $priority             当前语境的优先级
 * @property-read string $stageName         当前节点所属的 stage 名称
 * @property-read string[] $forwards        接下来要经过的 stage
 *
 */
interface Node extends ArrayAndJsonAble
{

    public function toThread() : Thread;

    public function getStageFullname() : string;

    /**
     * @return bool
     */
    public function next() : bool;

    /**
     * 预订接下来要经过的 Stage
     * @param array $stageNames
     */
    public function pushStack(array $stageNames) : void;


    /**
     * 重置管道
     */
    public function flushStack() : void;


    /*-------- find ---------*/

    public function findStageDef(Conversation $conversation) : StageDef;

    public function findContext(Conversation $conversation) : Context;
}