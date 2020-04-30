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
 * @property-read string[] $next
 * @property-read int $priority
 * @property-read int $gc
 */
interface Frame
{
    public function next() : bool;

    public function forward(array $stages) : void;

    public function reset(array $stages) : void;


    public function findContext(Cloner $cloner) : Context;

    public function findStageDef(Cloner $cloner) : StageDef;

    public function findContextDef(Cloner $cloner) : ContextDef;
}