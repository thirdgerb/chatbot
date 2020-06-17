<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Component;

use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Ghost\MindSelfRegister;
use Commune\Framework\Component\AComponentOption;
use Commune\Ghost\Providers\Psr4SelfRegisterLoader;


/**
 * Ghost 的专用组件.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class GhostComponent extends AComponentOption
{

    /**
     * 根据 psr4 规则, 预加载可以自注册的 Mindset 套件.
     *
     * @see MindSelfRegister
     *
     * @param App $app
     * @param array $namespaceToPaths
     */
    public function loadPsr4MindRegister(
        App $app,
        array $namespaceToPaths
    ) : void
    {
        $option = [
            'id' => static::class . ':' . Psr4SelfRegisterLoader::class,
            'psr4' => $namespaceToPaths,
        ];
        $provider = new Psr4SelfRegisterLoader($option);
        $app->getServiceRegistry()->registerProcProvider($provider, false);
    }

}