<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint;

use Commune\Container\ContainerContract;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Container extends ContainerContract
{

    /**
     * 使用 ServiceProvider 注册服务
     * @param string|ServiceProvider $provider
     * @param bool $atTopNotBottom  在头部,还是尾部
     */
    public function register($provider, bool $atTopNotBottom = false) : void;

}