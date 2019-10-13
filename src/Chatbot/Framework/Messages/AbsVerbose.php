<?php

/**
 * Class Verbose
 * @package Commune\Chatbot\Framework\Message\Verbose
 */

namespace Commune\Chatbot\Framework\Messages;


use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\Framework\Messages\Traits\Verbosely;

/**
 * 标准的文本消息
 */
abstract class AbsVerbose extends AbsConvoMsg implements VerboseMsg
{
    use Verbosely;

    /**
     * @var string
     */
    protected $_text;

    /**
     * Text constructor.
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->_text = $text;
        parent::__construct();
    }

    public function __sleep() : array
    {
        return array_merge(parent::__sleep(), [
            '_text',
            '_level'
        ]);
    }

    public function getText(): string
    {
        return $this->_text;
    }

    /**
     * 消息的数组化, 尽量用通用做法, 降低管理的困难.
     * @return array
     */
    public function toMessageData(): array
    {
        return get_object_vars($this);
    }

    /**
     * 判断是空输入.
     * 单个标点符号也认为是空输入.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        $trimmed = $this->getTrimmedText();
        return strlen($trimmed) === 0;
    }

}