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
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Shell\Exceptions\RendererNotFoundException;
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
     * @return RendererOption[]
     */
    public function getOptionMap() : array;


    /**
     * 根据 ID 获取一个 renderer
     *
     * @param ReqContainer $container
     * @param string $id        已经注册过的 ID, 或者 renderer 的类名.
     * @param array $params
     * @return Renderer
     */
    public function getRenderer(
        ReqContainer $container,
        string $id,
        array $params = []
    ) : Renderer;

    /**
     * @param ReqContainer $container
     * @param OutputMsg $output
     * @param ProtocalMatcher $matcher
     * @return array
     */
    public function render(
        ReqContainer $container,
        OutputMsg $output,
        ProtocalMatcher $matcher
    ) : array;

}