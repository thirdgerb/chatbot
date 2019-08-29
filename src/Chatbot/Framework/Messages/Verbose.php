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
 *
 * Class Verbose
 * @package Commune\Chatbot\Framework\Message\Verbose
 */
class Verbose extends AbsMessage implements VerboseMsg
{
    use Verbosely;

    const MESSAGE_TYPE = VerboseMsg::class;

    /**
     * @var string
     */
    protected $text;

    /**
     * Text constructor.
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->text = $text;
        parent::__construct();
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function toMessageData(): array
    {
        return [
            'text' => $this->text,
            'level' => $this->getLevel()
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