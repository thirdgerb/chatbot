<?php


namespace Commune\Support\OptionRepo\Storage\Memory;


use Commune\Support\Option;
use Commune\Support\OptionRepo\Contracts\OptionStorage;
use Commune\Support\OptionRepo\Options\StorageMeta;

class MemoryStorage implements OptionStorage
{

    protected static $options = [];

    /**
     * @param Option $option
     * @param MemoryStorageMeta $meta
     */
    public function save(
        Option $option,
        StorageMeta $meta
    ): void
    {
        $expire = $meta->expire ? time() + $meta->expire : 0;
        static::$options[get_class($option)][$option->getId()] = [$option, $expire];
    }

    public function get(
        string $optionName,
        string $id,
        StorageMeta $meta
    ): ? Option
    {
        $data = static::$options[$optionName][$id] ?? null;

        if (empty($data)) {
            return null;
        }

        list($option, $expire) = $data;

        if ($expire === 0 || time() < $expire) {
            return $option;
        }

        $this->delete($optionName, $meta, $id);
        return null;
    }

    public function has(
        string $optionName,
        string $id,
        StorageMeta $meta
    ): bool
    {
        $data = static::$options[$optionName][$id] ?? null;
        if (empty($data)) {
            return false;
        }
        list($option, $expire) = $data;
        return time() < $expire;
    }

    public function delete(string $optionName, StorageMeta $meta, string ...$ids): void
    {
        foreach ($ids as $id ) {
            unset(static::$options[$optionName][$id]);
        }
    }

    public function lockId(string $optionName, string $id, StorageMeta $meta): bool
    {
        return true;
    }


}