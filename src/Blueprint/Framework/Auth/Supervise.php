<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Framework\Auth;


/**
 * 是否是超级管理员.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Supervise extends Policy
{
    // 访客.
    const GUEST = 0;
    // 注册用户
    const USER = 1;
    // 超级管理员.
    const SUPERVISOR = 250;
    // 项目作者. 哈哈哈
    const AUTHOR = 255;
}