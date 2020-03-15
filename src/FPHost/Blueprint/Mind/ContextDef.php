<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\FPHost\Blueprint\Mind;

use Commune\FPHost\Blueprint\Meta\Wrapper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ContextDef extends Wrapper, MemoryDef
{

    /**
     * Context 名称
     * @return string
     */
    public function contextName() : string;

    public function isIntent() : bool;

    public function entityNames() : array;

    /*------- stage -------*/

    public function initialStageDef() : StageDef;

    public function invokeStageDef() : StageDef;

    public function hasStage(string $stageName) : bool;

    public function findStage(string $stageName) : StageDef;

    public function getAllStages() : array;


}