<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Cloner;


/**
 * 获取访问用户的身份信息
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ClonerGuest
{
    // 用户级别
    // 访客.
    const GUEST = 0;
    // 注册用户
    const USER = 1;
    // 超级管理员.
    const SUPERVISOR = 250;
    // 项目作者. 哈哈哈
    const AUTHOR = 255;

    /**
     * 用户 id
     * @return string
     */
    public function getId() : string;

    /**
     * 用户名称
     * @return string
     */
    public function getName() : string;

    /**
     * 是否来自另一个 Cloner 的消息.
     * @return bool
     */
    public function isFromBot() : bool;

    /**
     * 来自什么 App, 通常指来自的 shell
     * @return string
     */
    public function fromApp() : string;

    /**
     * 来自的 Session. 如果消息来自 shell, 则这里是 Shell 的 Session
     * 和 Cloner 的 session 并不一样.
     * @return string
     */
    public function fromSession() : string;


    /**
     * 获取用户的级别.
     * @return int
     */
    public function getLevel() : int;

    /**
     * 来自端上的全部数据.
     * @return array
     */
    public function getInfo() : array;
}