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

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface EmotionDef extends Def
{

    /**
     * 检查某种情绪是否匹配上了.
     * @param Cloner $cloner
     * @return bool
     */
    public function feels(Cloner $cloner) : bool;
}