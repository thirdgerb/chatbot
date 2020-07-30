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


/**
 * Ghost 分身对外展示的形象.
 * 可以通过这个组件来自定义.
 *
 * 这样可以在不同的 shell 有截然不同的对外形象.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ClonerAvatar
{
    /**
     * 分身对外呈现的 ID
     * @return string
     */
    public function getId() : string;

    /**
     * 分身对外呈现的名称
     * @return string
     */
    public function getName() : string;

    /**
     * 分身的完整配置.
     * @return array
     */
    public function getConfig() : array;

    /**
     * 通过预定义的 Presenter 来呈现分身, 暴露更精确的方法和属性.
     *
     * @param string $presenter
     * @return AvatarPresenter
     */
    public function present(string $presenter) : AvatarPresenter;
}