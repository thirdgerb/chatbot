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
use Commune\Framework\Bootstrap\LoadComponents;
use Commune\Ghost\Support\ValidateUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ShellLoadComponents extends LoadComponents
{
    /**
     * @param Shell $app
     * @return array
     */
    public function getComponentConfigs(App $app): array
    {
        ValidateUtils::isArgInstanceOf($app, Shell::class, true);
        return $app->getConfig()->components;
    }


}