<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Intercom;

use Commune\Protocals\Comprehension;
use Commune\Protocals\HostMsg;

/**
 * Ghost 的输入消息.
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 *
 * @property-read Comprehension $comprehension
 */
interface GhostInput extends GhostMsg
{

    /*----- 额外的信息 -----*/

    public function getSceneId() : string;

    public function getEnv() : array;

    /*----- 生成回复 -----*/

    public function output(HostMsg $message) : GhostOutput;

}