<?php

/**
 * Class Verbose
 * @package Commune\Chatbot\Framework\Message\Verbose
 */

namespace Commune\Chatbot\Framework\Messages;


use Commune\Chatbot\Blueprint\Message\VerboseMsg;

/**
 * 标准的文本消息
 *
 * Class Verbose
 * @package Commune\Chatbot\Framework\Message\Verbose
 */
class Verbose extends AbsMessage implements VerboseMsg
{
    use Verbosely;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var string
     */
    protected $input;

    /**
     * Text constructor.
     * @param string $input
     */
    public function __construct(string $input)
    {
        $this->input = $input;
        parent::__construct();
    }

    public function getText(): string
    {
        return $this->text ?? $this->_translation ?? $this->getInput();
    }

    public function getInput(): string
    {
        return $this->input;
    }


    public function toMessageData(): array
    {
        return [
            'text' => $this->text,
            'level' => $this->_level
        ];
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

    public function namesAsDependency(): array
    {
        return array_merge(parent::namesAsDependency(), [VerboseMsg::class]);
    }

}