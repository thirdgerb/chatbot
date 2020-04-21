<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Shell;

use Commune\Blueprint\Platform\Shell;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellKernel
{

    /**
     * 处理一个输入流.
     *
     * @param Shell\InputReq $req
     * @return Shell\InputRes
     */
    public function onInput(Shell\InputReq $req) : Shell\InputRes;

    /**
     * 处理一个输出流
     *
     * @param Shell\OutputReq $req
     * @return Shell\OutputRes
     */
    public function onOutput(Shell\OutputReq $req) : Shell\OutputRes;
}