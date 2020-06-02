<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Registry\Meta;

use Commune\Support\Option\AbsOption;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class StorageOption extends AbsOption implements Wrapper
{

    public function toMeta(): Meta
    {
        $data = $this->toArray();
        return new StorageMeta([
            'wrapper' => static::class,
            'config' => $data,
        ]);
    }

    /**
     * @param StorageMeta $meta
     * @return Wrapper
     */
    public static function wrapMeta(Meta $meta): Wrapper
    {
        $config = $meta->config;
        return new static($config);
    }


    /**
     * StorageDriver Name
     * @return string
     */
    abstract public function getDriver() : string;
}