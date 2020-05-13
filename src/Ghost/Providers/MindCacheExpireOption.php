<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Providers;

use Commune\Support\Option\AbsOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read int $context
 * @property-read int $stage
 * @property-read int $intent
 * @property-read int $memory
 * @property-read int $emotion
 * @property-read int $entity
 * @property-read int $synonym
 */
class MindCacheExpireOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            'context' => 600,
            'stage' => 600,
            'intent' => 600,
            'memory' => 1000,
            'emotion' => 1000,
            'entity' => 1000,
            'synonym' => 1000,
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}