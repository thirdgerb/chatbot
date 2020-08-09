<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Bootstrap;

use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Ghost;
use Commune\Components\Predefined\PredefinedComponent;
use Commune\Framework\Bootstrap\LoadComponents;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GhostLoadComponent extends LoadComponents
{
    /**
     * Ghost 启动的时候默认加载 Predefined.
     * @param Ghost $app
     * @return array
     */
    public function getComponentConfigs(App $app): array
    {
        $config = $app->getConfig()->components;
        $predefined = PredefinedComponent::class;

        if (
            !array_key_exists($predefined, $config)
            && !in_array($predefined, $config)
        ) {
            $config[] = $predefined;
        }

        return $config;
    }

}