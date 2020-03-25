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

use Commune\Framework\Blueprint\App;
use Commune\Ghost\Blueprint\Kernels\ApiKernel;
use Commune\Ghost\Blueprint\Kernels\AsyncKernel;
use Commune\Ghost\Blueprint\Kernels\MessageKernel;
use Commune\Ghost\Blueprint\Meta\MetaRegistrar;
use Commune\Ghost\Blueprint\Definition\Mindset;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $chatbotName               机器人的名称
 *
 * 以下属性可以依赖注入
 *
 * @property-read GhostConfig $config               Ghost 配置
 * @property-read Mindset $mind                     对话机器人的思维. 公共的
 * @property-read MetaRegistrar $metaReg            元数据的注册表
 */
interface Ghost extends App
{

    public function getApiKernel() : ApiKernel;

    public function getCallbackKernel() : AsyncKernel;

    public function getMessageKernel() : MessageKernel;
}