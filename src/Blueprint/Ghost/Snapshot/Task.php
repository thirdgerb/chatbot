<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Snapshot;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Definition\ContextDef;
use Commune\Blueprint\Ghost\Definition\StageDef;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $contextId
 * @property-read string $contextName
 * @property-read string $stageName
 * @property-read array $query
 * @property-read string[] $stack
 * @property-read int $priority
 * @property-read int $gc
 */
interface Task
{
    /**
     * 进入下一个 Stage
     * @return bool
     */
    public function next() : bool;

    /**
     * @param string $stageName
     */
    public function changeStage(string $stageName) : void;

    /**
     *
     * @param string[] $stages
     */
    public function forward(array $stages) : void;

    /**
     * @param string|null $stageName  为 null 就是当前 Stage
     * @return string
     */
    public function fullStageName(string $stageName = null) : string;

    /**
     */
    public function reset() : void;

    /**
     * @param Cloner $cloner
     * @return Context
     */
    public function findContext(Cloner $cloner) : Context;

    /**
     * @param Cloner $cloner
     * @return StageDef
     */
    public function findStageDef(Cloner $cloner) : StageDef;

    /**
     * @param Cloner $cloner
     * @return ContextDef
     */
    public function findContextDef(Cloner $cloner) : ContextDef;
}