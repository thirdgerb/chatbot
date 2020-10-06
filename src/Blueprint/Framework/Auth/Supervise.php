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

use Commune\Blueprint\Ghost\Cloner\ClonerGuest;

/**
 * 是否是超级管理员.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Supervise extends Policy
{
    // 访客.
    const GUEST = ClonerGuest::GUEST;
    // 注册用户
    const USER = ClonerGuest::USER;
    // 超级管理员.
    const SUPERVISOR = ClonerGuest::SUPERVISOR;
    // 项目作者. 哈哈哈
    const AUTHOR = ClonerGuest::AUTHOR;
}