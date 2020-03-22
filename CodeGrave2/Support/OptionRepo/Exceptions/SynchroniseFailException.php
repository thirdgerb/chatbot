<?php


namespace Commune\Support\OptionRepo\Exceptions;

/**
 * 同步数据失败.
 */
class SynchroniseFailException extends OptionRepoException
{
    public function __construct(string $optionName, string $id, string $reason)
    {
        parent::__construct("synchronise option $optionName of id $id failed because $reason");
    }
}