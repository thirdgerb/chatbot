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

use Commune\Blueprint\Framework\Session\SessionScene;
use Commune\Blueprint\Ghost\Ucl;


/**
 * 当前请求的场景信息.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ClonerScene extends SessionScene
{
    /**
     * 获取场景名称.
     * 是 shell 与 ghost 沟通的一种方式
     * ghost 可以通过 scene, 了解 shell 的请求场景.
     *
     * @return string
     */
    public function getName() : string;

    /**
     * shell 端设定的当前对话的入口节点
     * 既是 root, 也是 entry
     *
     * @return Ucl
     */
    public function getEntry() : Ucl;
}