<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Blueprint\Reaction;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Renderer
{
    public function hasTemplate(string $reactionId) : bool;

    public function getTemplate(string $reactionId) : Template;

    public function defaultTemplates() : Template;


    public function getTemplateNames() : array;

    public function getRegistered() : array;

}