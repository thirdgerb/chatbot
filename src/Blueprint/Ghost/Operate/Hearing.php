<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Operate;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Tools\Matcher;

/**
 * 用户视角对话状态的回归.
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read Dialog $dialog
 */
interface Hearing extends Matcher
{
    public function getDialog() : Dialog;

    public function todo(callable $caller) : Hearing;

    public function then(callable $caller = null) : Hearing;

    public function component(callable $caller) : Hearing;

    public function fallback(callable $caller) : Hearing;

    public function end() : Dialog;
}