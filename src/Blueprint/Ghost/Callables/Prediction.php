<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Callables;

use Commune\Blueprint\Ghost\Match\Matcher;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Prediction
{
    public function __construct(Matcher $matcher);

    public function __invoke() : bool;
}