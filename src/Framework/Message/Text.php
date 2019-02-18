<?php

/**
 * Class Text
 * @package Commune\Chatbot\Framework\Message
 */

namespace Commune\Chatbot\Framework\Message;

class Text extends Message
{
    const PATTERN  = '';

    const INFO = 2;
    const WARN = 4;
    const ERROR = 8;

    protected $text;

    protected $style;

    public function __construct(string $text, int $style = self::INFO, string $verbose = Message::NORMAL)
    {
        $this->text = $text;
        $this->style = $style;
        parent::__construct($verbose);
    }

    /**
     * @return int
     */
    public function getStyle(): int
    {
        return $this->style;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getData(): array
    {
        return [
            'text' => $this->getText()
        ];
    }
}