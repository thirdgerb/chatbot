<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\FPHost\Blueprint;


/**
 * 对话机器人的思维内核, 管理所有对话逻辑.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Host
{

    public function session(): Session;

    public function mind() : Mind;

    public function dialog() : Dialog;

    public function speech() : Speech;

    public function memory() : Memory;

}