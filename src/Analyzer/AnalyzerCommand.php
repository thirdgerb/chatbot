<?php

/**
 * Class AnalyzerCommand
 * @package Commune\Chatbot\Analyzer
 */

namespace Commune\Chatbot\Analyzer;


use Commune\Chatbot\Framework\Conversation\Conversation;
use Illuminate\Console\Parser;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;

abstract class AnalyzerCommand
{

    protected $signature = '';

    protected $description = '';

    protected  $name;

    protected  $arguments = [];

    protected  $options = [];

    public function __construct()
    {
        [$name, $arguments, $options] = Parser::parse($this->signature);
        $this->name = $name;
        $this->arguments = $arguments;
        $this->options = $options;
    }

    public function match(InputInterface $input) : bool
    {
        if ($this->name !== $input->getFirstArgument()) {
            return false;
        }

        $defined = count($this->arguments);
        $args = count($input->getArguments());
        return $defined === $args;
    }

    abstract public function handle(InputInterface $input, Conversation $conversation): Conversation;
}