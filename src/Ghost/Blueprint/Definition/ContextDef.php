<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Definition;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Convo\ConvoScope;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ContextDef extends Def
{

    /**
     * 公共类意图可以被全局访问到.
     * 否则无法用意图的方式命中.
     * @return bool
     */
    public function isPublic() : bool;

    public function getPriority() : int;

    public function entityNames() : array;

    public function makeId(ConvoScope $scope) : string;

    /*------- methods -------*/

    public function newContext(array $assignment = []) : Context;

    /*------- stage -------*/

    public function getInitialStageDef() : StageDef;

    public function hasStage(string $stageName) : bool;

    public function getStage(string $stageName) : StageDef;

    public function getAllStages() : array;



}