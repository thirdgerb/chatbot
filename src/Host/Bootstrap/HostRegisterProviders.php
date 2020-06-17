<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Host\Bootstrap;

use Commune\Blueprint\Host;
use Commune\Blueprint\Framework\App;
use Commune\Ghost\Support\ValidateUtils;
use Commune\Framework\Bootstrap\RegisterProviders;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class HostRegisterProviders extends RegisterProviders
{
    /**
     * @param Host $app
     * @return array
     */
    public function getProviderConfigs(App $app): array
    {
        ValidateUtils::isArgInstanceOf($app, Host::class, true);
        return $app->getConfig()->providers;
    }


}