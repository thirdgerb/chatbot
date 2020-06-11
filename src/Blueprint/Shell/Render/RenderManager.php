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


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface RenderManager
{
    /**
     * @param Renderer $renderer
     */
    public function register(Renderer $renderer) : void;

    /**
     * @return Renderer[]
     */
    public function getRenders() : array;

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
     * @param RenderOption[] $protocals
     * @return OutputMsg[]
     */
    public function render(
        Session $session,
        OutputMsg $message,
        array $protocals
    ) : array;

}