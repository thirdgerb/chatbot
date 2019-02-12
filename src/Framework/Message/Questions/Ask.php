<?php

/**
 * Class Ask
 * @package Commune\Chatbot\Framework\Message\Questions
 */

namespace Commune\Chatbot\Framework\Message\Questions;

use Commune\Chatbot\Framework\Message\Text;

class Ask extends Text
{

    private $question;

    private $default;

    public function __construct(string $question, string $default = null)
    {
        $this->question = $question;
        $this->default = $default;
        $text = "$question : [$default]";
        parent::__construct($text);
    }

    /**
     * @return string
     */
    public function getQuestion(): string
    {
        return $this->question;
    }

    /**
     * @return string
     */
    public function getDefault(): string
    {
        return $this->default;
    }

}