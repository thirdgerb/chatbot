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
 *
 * @property-read string $name
 */
abstract class StorageOption extends AbsOption implements Wrapper
{
    const IDENTITY = 'name';

    public function getMeta(): Meta
    {
        $data = $this->toArray();
        $name = $data['name'];
        unset($data['name']);

        return new StorageMeta([
            'name' => $name,
            'wrapper' => static::class,
            'config' => $data,
        ]);
    }

    /**
     * @param StorageMeta $meta
     * @return Wrapper
     */
    public static function wrap(Meta $meta): Wrapper
    {
        $name = $meta->name;
        $config = $meta->config;
        $config['name'] = $name;

        return new static($config);
    }


    /**
     * StorageDriver Name
     * @return string
     */
    abstract public function getDriver() : string;
}