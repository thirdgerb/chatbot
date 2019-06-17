<?php

/**
 * Class PropertyReaderParser
 * @package Commune\Support
 */

namespace Commune\Support\Property;


interface PropertyReaderParser
{
    public function getOriginType() : string;

    public function getter($originData, string $key);
}