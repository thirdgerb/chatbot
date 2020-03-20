<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Blueprint\Session;

use Commune\Chatbot\Ghost\Blueprint\Exceptions\TooManyRedirectsException;
use Commune\Chatbot\Ghost\Blueprint\Redirector;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 用于记录 Redirector 的操作.
 * 同时会记录 Redirector 的次数. 高于阈值时会触发异常.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Tracker extends ArrayAndJsonAble
{

    /**
     *
     * @param Redirector $redirector
     * @throws TooManyRedirectsException
     */
    public function record(Redirector $redirector) : void;

    public function brief() : string;
}