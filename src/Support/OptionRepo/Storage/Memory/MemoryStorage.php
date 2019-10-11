<?php


namespace Commune\Support\OptionRepo\Storage\Memory;


use Commune\Support\Option;
use Commune\Support\OptionRepo\Contracts\OptionStorage;
use Commune\Support\OptionRepo\Options\CategoryMeta;
use Commune\Support\OptionRepo\Options\StorageMeta;

class MemoryStorage implements OptionStorage
{

    protected static $options = [];

    /**
     * @param CategoryMeta $category
     * @param MemoryStorageMeta $storage
     * @param Option ...$options
     */
    public function save(
        CategoryMeta $category,
        StorageMeta $storage,
        Option ...$options
    ): void
    {
        $expire = $storage->expire ? time() + $storage->expire : 0;
        foreach ($options as $option) {
            static::$options[$category->getId()][$option->getId()] = [$option, $expire];
        }
    }

    public function flush(
        CategoryMeta $category,
        StorageMeta $storage
    ): void
    {
        unset(static::$options[$category->getId()]);
    }


    public function get(
        CategoryMeta $category,
        StorageMeta $storage,
        string $id
    ): ? Option
    {
        $data = static::$options[$category->getId()][$id] ?? null;

        if (empty($data)) {
            return null;
        }

        list($option, $expire) = $data;

        if ($expire === 0 || time() < $expire) {
            return $option;
        }

        $this->delete($category, $storage, $id);
        return null;
    }

    public function has(
        CategoryMeta $category,
        StorageMeta $storage,
        string $id
    ): bool
    {
        $data = static::$options[$category->getId()][$id] ?? null;
        if (empty($data)) {
            return false;
        }
        list($option, $expire) = $data;
        return time() < $expire;
    }

    public function delete(CategoryMeta $category, StorageMeta $storage, string ...$ids): void
    {
        foreach ($ids as $id ) {
            unset(static::$options[$category->getId()][$id]);
        }
    }

    public function lockId(
        CategoryMeta $category,
        StorageMeta $storage,
        string $id
    ): bool
    {
        return true;
    }


}