<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint;

use Commune\Chatbot\Contracts\Messenger;
use Commune\Framework\Blueprint\App;
use Commune\Ghost\Blueprint\Kernels\ApiKernel;
use Commune\Ghost\Blueprint\Kernels\CallbackKernel;
use Commune\Ghost\Blueprint\Kernels\MessageKernel;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read GhostConfig $config
 */
interface Ghost extends App
{
    /**
     * 初始化 Ghost
     */
    public function boot() : void;


    /**
     * Shell 与 Ghost 通讯的通道.
     * @return Messenger
     */
    public function getMessenger() : Messenger;



    public function getApiKernel() : ApiKernel;

    public function getCallbackKernel() : CallbackKernel;

    public function getMessageKernel() : MessageKernel;
}