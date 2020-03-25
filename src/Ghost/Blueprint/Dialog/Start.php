<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Dialog;

use Commune\Ghost\Blueprint\Dialog;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Routing\Fallback;
use Commune\Ghost\Blueprint\Routing\Redirect;
use Commune\Ghost\Blueprint\Routing\Staging;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 更多属性
 * @see Dialog
 */
interface Start extends Dialog
{

    /**
     * 等待用户消息
     * @return Operator
     */
    public function listen() : Operator;

    /**
     * @return Operator
     */
    public function subProcess() : Operator;


    public function staging() : Staging;

    public function redirect() : Redirect;

    public function fallback() : Fallback;

}