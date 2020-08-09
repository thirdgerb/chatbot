<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Bootstrap;

use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Shell;
use Commune\Framework\Bootstrap\RegisterProviders;
use Commune\Support\Utils\TypeUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ShellRegisterProviders extends RegisterProviders
{
    /**
     * @param Shell $app
     * @return array
     */
    public function getProviderConfigs(App $app): array
    {
        TypeUtils::validateInstance($app, Shell::class, __METHOD__);
        return $app->getConfig()->providers;
    }


}