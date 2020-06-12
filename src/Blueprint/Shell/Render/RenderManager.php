<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Shell\Render;
use Commune\Blueprint\Configs\RenderOption;
use Commune\Blueprint\Framework\Session;
use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\OutputMsg;
use Commune\Support\Protocal\ProtocalMatcher;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface RenderManager
{
    /**
     * 注册一个 Renderer
     * @param RendererOption $option
     */
    public function register(RendererOption $option) : void;

    /**
     * 根据 ID 获取一个 renderer
     * @param string $id
     * @return Renderer
     */
    public function getRenderer(string $id) : Renderer;

    /**
     * @return RendererOption[]
     */
    public function getOptions() : array;

    /**
     * @return Renderer
     */
    public function getDefaultRender() : Renderer;

    /**
     * @param HostMsg $message
     * @param RenderOption[] $protocals
     * @return Renderer
     */
    public function matchRenderer(HostMsg $message, array $protocals) : Renderer;

    /**
     * @param Session $session
     * @param OutputMsg $message
     * @param ProtocalMatcher $matcher
     * @return OutputMsg[]
     */
    public function render(
        Session $session,
        OutputMsg $message,
        ProtocalMatcher $matcher
    ) : array;

}