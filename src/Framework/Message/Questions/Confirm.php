<?php

/**
 * Class Confirm
 * @package Commune\Chatbot\Framework\Message\Questions
 */

namespace Commune\Chatbot\Framework\Message\Questions;


use Commune\Chatbot\Framework\Message\Text;

class Confirm extends Text
{

    /**
     * @var string 
     */
    protected $question;

    /**
     * @var string 
     */
    protected $default;

    public function __construct(string $question, string $default = 'yes')
    {
        $this->question = $question;
        $this->default = $default;
        parent::__construct("$question: [$default]");
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