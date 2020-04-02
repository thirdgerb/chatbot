<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Blueprint\Render;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Renderer
{

    /**
     * 注册一组 Reaction 与模板的对应关系.
     * @param string $intentNamePrefix
     * @param string $templateName
     */
    public function register(string $intentNamePrefix, string $templateName) : void;

    /**
     * 获取一个 intentName 对应的模板.
     * @param string $intentName
     * @return Template|null
     */
    public function findTemplate(string $intentName) : ? Template;

    /**
     * 获得系统的默认模板.
     * @return Template|null
     */
    public function getDefaultTemplate() : ? Template;
}