<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Predefined;

use Commune\Blueprint\Framework\App;
use Commune\Ghost\Component\GhostComponent;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class PredefinedComponent extends GhostComponent
{
    public static function stub(): array
    {
        return [];
    }

    public static function relations(): array
    {
        return [];
    }

    public function bootstrap(App $app): void
    {
        $this->loadPsr4MindRegister(
            $app,
            [
                "Commune\\Components\\Predefined\\Memory" => __DIR__ . '/Memory',
            ]
        );
    }


}