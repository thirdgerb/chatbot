<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Meta;


/**
 * 元数据.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class Meta
{
    const NAME = 'name';
    const WRAPPER = 'wrapper';
    const ATTRIBUTES = 'attrs';

    protected static $relations = [
    ];

    protected $name;

    protected $wrapper;

    protected $attrs;

    protected $related = [];

    /**
     * Meta constructor.
     * @param array $metaData
     * @throws MetaException
     */
    public function __construct(array $metaData)
    {
        $this->name = $metaData[self::NAME];
        $this->wrapper = $metaData[self::WRAPPER] ?? '';
        $this->attrs = $metaData[self::ATTRIBUTES] ?? [];

        if (empty($this->name)) {
            throw new MetaException('meta name miss');
        }

        if (!is_string($this->wrapper)) {
            throw new MetaException('wrapper should be string');
        }

        if (!is_a($this->wrapper, Wrapper::class, TRUE)) {
            throw new MetaException('wrapper should implements ' . Wrapper::class);
        }

        if (!is_array($this->attrs)) {
            throw new MetaException('attributes should be array');
        }
    }

    public function toWrapper() : Wrapper
    {
        $abstract = $this->wrapper;
        return new $abstract($this);
    }

    public function __get($name)
    {
        // 检查是不是关联对象


        // 检查是不是列表


        return $this->attrs[$name] ?? null;
    }
}