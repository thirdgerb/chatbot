<?php

namespace Commune\Support\Schematic;

use Closure;
use InvalidArgumentException;


class Entries implements \IteratorAggregate, IEntries, \ArrayAccess
{

	/**
	 * @var array
	 */
	private $items;

	/**
	 * @var string
	 */
	private $entryClass;

	/**
	 * @var array
	 */
	private $cachedItems = [];


	/**
	 * @param array $items
	 * @param string $entryClass
	 */
	public function __construct(array $items, $entryClass = Entry::class)
	{
		$this->items = $items;
		$this->entryClass = $entryClass;
	}

    /**
     * @return Entry[]
     */
	public function toArray()
    {
        $keys = array_keys($this->items);
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key);
        }
        return $result;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->toArray());
    }


    /**
	 * @return int
	 */
	public function count()
	{
		return count($this->items);
	}


	/**
	 * @param int|string $key
	 * @return bool
	 */
	public function has($key)
	{
		return array_key_exists($key, $this->items);
	}


	/**
	 * @param int|string $key
	 * @return Entry
	 */
	public function get($key)
	{
		$this->validateKey($key);

		if (array_key_exists($key, $this->cachedItems)) {
			return $this->cachedItems[$key];
		}

		$entryClass = $this->entryClass;

		return $this->cachedItems[$key] = new $entryClass($this->items[$key], get_called_class());
	}


	/**
	 * @param array $keys
	 * @return static
	 */
	public function remove(...$keys)
	{
		$this->validateKeys($keys);

		$items = array_diff_key($this->items, array_flip($keys));

		return new static($items, $this->entryClass);
	}


	/**
	 * @param array $keys
	 * @return static
	 */
	public function reduceTo(...$keys)
	{
		$this->validateKeys($keys);

		$items = array_intersect_key($this->items, array_flip($keys));

		return new static($items, $this->entryClass);
	}


	/**
	 * @param Closure $callback
	 * @param string|NULL $entryClass
	 * @return static
	 */
	public function transform(Closure $callback, $entryClass = NULL)
	{
		return new static($callback($this->items), $entryClass !== NULL ? $entryClass : $this->entryClass);
	}


	/**
	 * @param int|string $key
	 */
	private function validateKey($key)
	{
		if (!array_key_exists($key, $this->items)) {
			throw new InvalidArgumentException("Missing entry with key $key.");
		}
	}


	/**
	 * @param array $keys
	 */
	private function validateKeys($keys)
	{
		$keys = array_flip($keys);

		$missingKeys = array_diff_key($keys, $this->items);
		if ($missingKeys !== []) {
			throw new InvalidArgumentException('Missing entries with keys: ' . implode(', ', array_keys($missingKeys)) . '.');
		}
	}

    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException(
            static::class . '::'. __FUNCTION__
            . ' not allowed'
        );
    }

    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException(
            static::class . '::'. __FUNCTION__
            . ' not allowed'
        );
    }


}
