<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\FPHost\Blueprint;

use Commune\FPHost\Blueprint\Meta\Registrar;
use Commune\FPHost\Blueprint\Mind\ContextDef;
use Commune\FPHost\Blueprint\Mind\Corpus;

/**
 * 对话机器人的静态思维管理.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Mind
{

    /*------- context -------*/

    public function hasContextDef(string $contextName) : bool;

    public function findContextDef(string $contextName) : ContextDef;

    public function getContextDefs(string $contextPrefix) : array;

    /*------- corpus -------*/

    public function corpus() : Corpus;

    /*------- metas -------*/

    /**
     * 语境注册表
     * @return Registrar
     */
    public function contextReg() : Registrar;

    /**
     * 对话单元注册表
     * @return Registrar
     */
    public function stageReg() : Registrar;

    /**
     * 记忆注册表
     * @return Registrar
     */
    public function memoryReg() : Registrar;

    /**
     * 意图注册表
     * @return Registrar
     */
    public function intentReg() : Registrar;

    /**
     * 实体注册表
     * @return Registrar
     */
    public function entityReg() : Registrar;

    /**
     * 同义词注册表
     * @return Registrar
     */
    public function synonymReg() : Registrar;


    public function speechReg() : Registrar;
}