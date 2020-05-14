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
use Commune\Blueprint\Ghost\Memory\Recollection;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface MemoryDef extends Def
{

    public function isLongTerm() : bool;

    /**
     * 记忆的上下文作用域.
     * @see Cloner\ClonerScope
     * @return string[]
     */
    public function getScopes() : array;

    /**
     * @return DefParamsCollection
     */
    public function getParams() : DefParamsCollection;

    /**
     * 记忆体的默认值.
     * @return array
     */
    public function getDefaults() : array;

    /**
     * 获取一个记忆体的实例.
     * @param Cloner $cloner
     * @return Recollection
     */
    public function recall(Cloner $cloner) : Recollection;

}