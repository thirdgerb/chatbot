<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Blueprint;

use Commune\Framework\Blueprint\Application;
use Commune\Shell\Blueprint\Kernels\RequestKernel;
use Commune\Shell\Blueprint\Render\Renderer;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 相关属性
 *
 */
interface Shell extends Application
{

    public function getChatbotName() : string;

    public function getShellName() : string;

    /**
     * @return RequestKernel
     */
    public function getReqKernel() : RequestKernel;

    public function getRenderer() : Renderer;
}