<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Mind;


/**
 * 对话机器人的思维.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Mindset
{

    /*--------- context ---------*/


    /*--------- intent ---------*/

    public function hasIntentDef(string $intentName) : bool;

    public function getIntentNamesByPrefix(string $intentNamePrefix) : array;

    public function countIntentsByPrefix(string $intentNamePrefix) : int;

    public function getIntentDef(string $intentName) : IntentDef;

    public function registerIntentDef(IntentDef $def) : void;

}