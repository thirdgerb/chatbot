<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Struct;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id            meta 的 Id
 * @property-read string $wrapper       meta 的包装类对象
 * @property-read array $config         meta 的原始数据
 */
abstract class AbsMeta extends AbsStruct implements Meta
{
    const IDENTITY = 'id';

    final public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    public function getMetaType(): string
    {
        return static::class;
    }

    public function wrap(): Struct
    {
        $wrapper = $this->wrapper;
        return new $wrapper($this->config) ;
    }
    public static function stub(): array
    {
        return [
            'id' => 'id',
            'wrapper' => '',
            'config' => [],
        ];
    }

    public static function validate(array $data): ? string
    {
        $id = $data['id'] ?? null;
        $wrapper = $data['wrapper'] ?? null;
        $config = $data['config'] ?? [];

        if (!is_string($id)) {
            return 'field id is invalid';
        }

        if (!is_string($wrapper) || !is_a($wrapper, Struct::class, TRUE)) {
            return 'field wrapper is invalid';
        }

        if (!is_array($config)) {
            return 'field config is invalid';
        }

        return null;
    }


}