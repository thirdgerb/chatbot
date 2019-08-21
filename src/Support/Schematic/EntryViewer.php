<?php

namespace Commune\Support\Schematic;

use Closure;
use Traversable;


class EntryViewer
{

	/**
	 * @param mixed $entry
	 * @param Closure $converter
	 * @return object|NULL
	 */
	public static function viewEntry($entry, Closure $converter)
	{
		return self::convert($converter, $entry);
	}


	/**
	 * @param array|Traversable $entries
	 * @param Closure $singleEntryConverter
	 * @return object[]
	 */
	public static function viewEntries($entries, Closure $singleEntryConverter)
	{
		$result = [];

		foreach ($entries as $index => $entry) {
			$entry = self::convert($singleEntryConverter, $entry, $index);

			if ($entry !== NULL) {
				$result[$index] = $entry;
			}
		}

		return $result;
	}


	/**
	 * @param Closure $converter
	 * @param mixed $entry
	 * @param mixed|NULL $index
	 * @return object|NULL
	 */
	private static function convert(Closure $converter, $entry, $index = NULL)
	{
		$entry = $index === NULL ? call_user_func($converter, $entry) : call_user_func($converter, $entry, $index);

		return $entry !== NULL ? (object) $entry : NULL;
	}

}
