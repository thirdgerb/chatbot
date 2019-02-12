<?php

/**
 * Class Confirm
 * @package Commune\Chatbot\Framework\Message\Questions
 */

namespace Commune\Chatbot\Framework\Message\Questions;


use Commune\Chatbot\Framework\Message\Text;

class Confirm extends Text
{

    private $question;

    private $default;

    public function __construct(string $question, bool $default = true)
    {
        $this->question = $question;
        $this->default = $default;
        $defaultInfo = $default ? '是' : '否';
        parent::__construct("$question : [$defaultInfo]");
    }

    /**
     * @return string
     */
    public function getQuestion(): string
    {
        return $this->question;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->default;
    }

}