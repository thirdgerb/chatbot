<?php


namespace Commune\Support\OptionRepo\Exceptions;

/**
 * 仓库没有定义.
 */
class RepositoryMetaNotExistsException extends OptionRepoException
{
    public function __construct(string $optionName)
    {
        parent::__construct("option repository meta of $optionName not exists");
    }
}