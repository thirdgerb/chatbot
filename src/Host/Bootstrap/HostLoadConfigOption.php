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
use Commune\Framework\Bootstrap\LoadConfigOption;
use Commune\Support\Utils\TypeUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class HostLoadConfigOption extends LoadConfigOption
{
    /**
     * @param Host $app
     * @return array
     */
    protected function getConfigOptions(App $app): array
    {
        TypeUtils::validateInstance($app, Host::class, static::class . '::'. __FUNCTION__);
        return $app->getConfig()->options;
    }

}