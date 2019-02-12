<?php

/**
 * Class Choose
 * @package Commune\Chatbot\Framework\Message\Questions
 */

namespace Commune\Chatbot\Framework\Message\Questions;


use Commune\Chatbot\Framework\Message\Text;

class Choose extends Text
{

    /**
     * @var array
     */
    private $choices;

    private $default;

    public function __construct(string $question, array $choices, string $default = null)
    {
        $this->choices = $choices;
        $this->default = $default;
        $text = "$question : [$default] \n";
        foreach ($choices as $key => $val) {
            $text .= "[$key] $val \n";
        }
        parent::__construct($text);
    }

    /**
     * @return array
     */
    public function getChoices(): array
    {
        return $this->choices;
    }

    /**
     * @return string
     */
    public function getDefault(): string
    {
        return $this->default;
    }


}