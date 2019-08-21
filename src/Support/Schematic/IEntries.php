<?php

namespace Commune\Support\Schematic;

use Countable;
use Traversable;


interface IEntries extends Traversable, Countable
{

	/**
	 * @return Entry[]
	 */
	public function toArray();

}
