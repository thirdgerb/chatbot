<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Providers\Option;

use Commune\Support\Option\AbsOption;
use Commune\Support\Registry\Meta\StorageMeta;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read StorageMeta|null $context
 * @property-read StorageMeta|null $stage
 * @property-read StorageMeta|null $intent
 * @property-read StorageMeta|null $emotion
 * @property-read StorageMeta|null $entity
 * @property-read StorageMeta|null $synonym
 */
class MindStorageOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            'context' => null,
            'stage' => null,
            'intent' => null,
            'memory' => null,
            'emotion' => null,
            'entity' => null,
            'synonym' => null,
        ];
    }

    public static function relations(): array
    {
        return [
            'context' => StorageMeta::class,
            'stage' => StorageMeta::class,
            'intent' => StorageMeta::class,
            'memory' => StorageMeta::class,
            'emotion' => StorageMeta::class,
            'entity' => StorageMeta::class,
            'synonym' => StorageMeta::class,
        ];
    }


}