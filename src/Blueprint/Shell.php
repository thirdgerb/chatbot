<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint;

use Commune\Blueprint\Configs\ShellConfig;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Shell\Handlers\InputMessageParser;
use Commune\Blueprint\Shell\Handlers\ShlApiHandler;
use Commune\Blueprint\Shell\Handlers\ShlInputReqHandler;
use Commune\Blueprint\Shell\Handlers\ShlOutputReqHandler;
use Commune\Blueprint\Shell\Render\Renderer;
use Commune\Blueprint\Shell\Requests\ShellRequest;
use Commune\Blueprint\Shell\Requests\ShlInputRequest;
use Commune\Blueprint\Shell\Requests\ShlOutputRequest;
use Commune\Blueprint\Shell\Responses\ShellResponse;
use Commune\Blueprint\Shell\Responses\ShlOutputResponse;
use Commune\Blueprint\Shell\ShellSession;
use Commune\Protocals\HostMsg;
use Commune\Protocals\HostMsg\Convo\ApiMsg;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Protocals\Intercom\OutputMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Shell extends App
{
    /**
     * @return ShellConfig
     */
    public function getConfig() : ShellConfig;

    /**
     * @param InputMsg $input
     * @return ShellSession
     */
    public function newSession(InputMsg $input) : ShellSession;

    /**
     * @param ShellRequest $request
     * @return ShellResponse
     */
    public function handleRequest(ShellRequest $request) : ShellResponse;


    /*------ protocal handler ------*/

    /**
     * @param ReqContainer $container
     * @param ShlInputRequest $request
     * @return ShlInputReqHandler|null
     */
    public function getInputReqHandler(
        ReqContainer $container,
        ShlInputRequest $request
    ) : ? ShlInputReqHandler;

    /**
     * @param ReqContainer $container
     * @param ShlOutputRequest $request
     * @return ShlOutputResponse|null
     */
    public function getOutputReqHandler(
        ReqContainer $container,
        ShlOutputRequest $request
    ) : ? ShlOutputReqHandler;


    public function getApiHandler(ReqContainer $container, ApiMsg $message) : ? ShlApiHandler;

    /**
     * 输入消息的处理工具.
     * 返回 InputMsg
     *
     * @param ReqContainer $container
     * @param HostMsg $message
     * @return InputMessageParser|null
     */
    public function getInputParser(ReqContainer $container, HostMsg $message) : ? InputMessageParser;


    /**
     * 输出消息的渲染器.
     * 返回 InputMsg[]
     *
     * @param ReqContainer $container
     * @param HostMsg $message
     * @return callable|null
     */
    public function getOutputRenderer(ReqContainer $container, HostMsg $message) : ? Renderer ;

}