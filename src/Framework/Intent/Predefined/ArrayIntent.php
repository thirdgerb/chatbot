<?php

/**
 * Class Intent
 * @package Commune\Chatbot\Framework\Intent
 */

namespace Commune\Chatbot\Framework\Intent\Predefined;

use Commune\Chatbot\Framework\Message\Message;
use Commune\Chatbot\Framework\Intent\Intent;

class ArrayIntent extends Intent
{

    /*--------- definition operation ---------*/

    public function __construct(string $id, Message $message, array $entities = [])
    {
        parent::__construct($id, $message, $entities);
        $this->tokens = $entities;
    }

    /**
     * {@inheritdoc}
     */
    protected function parse(array $tokens)
    {
        foreach ($tokens as $key => $value) {
            if ('--' === $key) {
                return;
            }
            if (0 === strpos($key, '--')) {
                $this->addLongOption(substr($key, 2), $value);
            } elseif ('-' === $key[0]) {
                $this->addShortOption(substr($key, 1), $value);
            } else {
                $this->addArgument($key, $value);
            }
        }
    }


    /**
     * Adds a long option value.
     *
     * @param string $name  The long option key
     * @param mixed  $value The value for the option
     */
    protected  function addLongOption($name, $value)
    {
        if (!$this->definition->hasOption($name)) {
            return;
        }

        $option = $this->definition->getOption($name);

        if (null === $value) {
            if ($option->isValueRequired()) {
                $this->errors->add("--$name", sprintf('The "--%s" option requires a value.', $name));
                return;
            }

            if (!$option->isValueOptional()) {
                $value = true;
            }
        }

        $this->entities->put("--$name", $value);
    }


    /**
     * Adds a short option value.
     *
     * @param string $shortcut The short option key
     * @param mixed  $value    The value for the option
     */
    protected  function addShortOption($shortcut, $value)
    {
        if (!$this->definition->hasShortcut($shortcut)) {
            $this->errors->add("-$shortcut", sprintf('The "-%s" option does not exist.', $shortcut));
            return;
        }

        $this->addLongOption($this->definition->getOptionForShortcut($shortcut)->getName(), $value);
    }


    /**
     * Adds an argument value.
     *
     * @param string $name  The argument name
     * @param mixed  $value The value for the argument
     */
    protected  function addArgument($name, $value)
    {
        if (!$this->definition->hasArgument($name)) {
            $this->errors->add($name, sprintf('The "%s" argument does not exist.', $name));
            return;
        }

        $this->entities->put($name, $value);
    }




}