<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Kernel\Protocals;

use Commune\Protocals\Comprehension;
use Commune\Protocals\Intercom\InputMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellInputRequest extends AppRequest, HasInput
{
    /**
     * @return bool
     */
    public function isAsync() : bool;


    /**
     * @return InputMsg
     */
    public function getInput(): InputMsg;

    /**
     * 从终端向服务端传输的自定义环境变量. 可以自行定义协议.
     * @return array
     */
    public function getEnv() : array;

    /**
     * 请求的场景. 一个 Ucl 的地址.
     * @return string
     */
    public function getScene() : string;

    /**
     * 对请求的抽象理解.
     * @return Comprehension
     */
    public function getComprehension() : Comprehension;

    /**
     * @param int $errcode
     * @param string $errmsg
     * @return ShellInputResponse
     */
    public function response(
        int $errcode = AppResponse::SUCCESS,
        string $errmsg = ''
    ) : ShellInputResponse;

}