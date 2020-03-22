<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Definitions;

use Commune\Ghost\Blueprint\Context;
use Commune\Ghost\Blueprint\Ghost;
use Commune\Ghost\Blueprint\Meta\Wrapper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ContextDef extends Wrapper, MemoryDef
{

    /**
     * Context 名称
     * @return string
     */
    public function getName() : string;

    public function isPublic() : bool;

    public function entityNames() : array;

    /*------- methods -------*/

    public function newContext(Ghost $ghost, array $assignment = []) : Context;

    /*------- stage -------*/

    public function initialStageDef() : StageDef;

    public function invokeStageDef() : StageDef;

    public function hasStage(string $stageName) : bool;

    public function findStage(string $stageName) : StageDef;

    public function getAllStages() : array;


}