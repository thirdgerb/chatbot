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

use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Handlers\GhtApiHandler;
use Commune\Blueprint\Ghost\Handlers\GhtRequestHandler;
use Commune\Blueprint\Ghost\Request\GhostRequest;
use Commune\Blueprint\Ghost\Request\GhostResponse;
use Commune\Protocals\HostMsg\Convo\ApiMsg;
use Commune\Protocals\Intercom\InputMsg;

/**
 * Host 的灵魂. 对话机器人的内核.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Ghost extends App
{

    /**
     * @return GhostConfig
     */
    public function getConfig() : GhostConfig;

    /**
     * @param InputMsg $input
     * @return Cloner
     */
    public function newCloner(InputMsg $input) : Cloner;

    /**
     * 处理 Ghost 的输入请求.
     * 仍然允许用协议的方式自定义各种处理逻辑.
     *
     * @param GhostRequest $request
     * @return GhostResponse
     */
    public function handleRequest(GhostRequest $request) : GhostResponse;


    /*------ 协议 ------*/

    /**
     * 根据协议获取请求的 handler
     *
     * @param ReqContainer $container
     * @param GhostRequest $request
     * @return callable|GhtRequestHandler|null
     */
    public function getRequestHandler(ReqContainer $container, GhostRequest $request) : ? GhtRequestHandler;

    /**
     * @param ReqContainer $container
     * @param ApiMsg $input
     * @return GhtApiHandler|null
     */
    public function getApiHandler(ReqContainer $container, ApiMsg $input) : ? GhtApiHandler;
}