<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Runtime;

use Commune\Support\Babel\BabelSerializable;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 需要长期保存的数据.
 */
interface Savable extends BabelSerializable
{
    /**
     * 保存的 ID
     * @return string
     */
    public function getSavableId() : string;

    public function isSaving() : bool;
}