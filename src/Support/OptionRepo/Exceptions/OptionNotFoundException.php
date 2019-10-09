<?php


namespace Commune\Support\OptionRepo\Exceptions;

class OptionNotFoundException extends OptionRepoException
{
    public function __construct(string $optionName, string $id)
    {
        parent::__construct("$optionName option of id $id not found");
    }

}