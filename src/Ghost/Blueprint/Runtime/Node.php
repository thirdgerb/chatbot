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

    /**
     * 切换当前 stage
     * @param string $stageName
     */
    public function goStage(string $stageName) : void;

    /**
     * 预订接下来要经过的 Stage
     * @param array $stageNames
     */
    public function goStagePipes(array $stageNames) : void;

    /**
     * 重置管道
     */
    public function resetPipes() : void;

    /**
     * 前进一个节点
     * @return bool
     */
    public function forward() : bool;

}