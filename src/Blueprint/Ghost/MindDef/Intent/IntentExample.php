<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\MindDef\Intent;


/**
 * 意图的例句.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface IntentExample
{

    /**
     * 例句的原始语句.
     * @return string
     */
    public function getOrigin() : string;

    /**
     * 例句去掉标记后的语句.
     * @return string
     */
    public function getText() : string;

    /**
     * @return ExampleEntity[]
     */
    public function getEntities() : array;

}