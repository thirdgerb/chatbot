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
use Commune\Framework\Bootstrap\LoadComponents;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GhostLoadComponent extends LoadComponents
{
    /**
     * @param Ghost $app
     * @return array
     */
    public function getComponentConfigs(App $app): array
    {
        return $app->getConfig()->components;
    }

    public function getAppType(): string
    {
        return Ghost::class;
    }


}