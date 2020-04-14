<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Stage;

use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Routing\Fallback;
use Commune\Ghost\Blueprint\Routing\Redirect;
use Commune\Ghost\Blueprint\Routing\Staging;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 更多属性
 * @see Stage
 */
interface Activate extends Stage
{

    /**
     * 等待用户消息
     * @return Operator
     */
    public function listen() : Operator;

    /**
     * @param string $belongsTo
     * @return Operator
     */
    public function toSubProcess(string $belongsTo) : Operator;

    public function staging() : Staging;

    public function redirect() : Redirect;

    public function fallback() : Fallback;

}