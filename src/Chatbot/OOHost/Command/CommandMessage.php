<?php

/**
 * Class CommandIntent
 * @package Commune\Chatbot\Framework\Intent
 */

namespace Commune\Chatbot\OOHost\Command;

use Commune\Chatbot\Blueprint\Message\Command\CmdMessage;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Messages\AbsMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;
use InvalidArgumentException;

class CommandMessage extends AbsMessage implements CmdMessage
{

    const REGEX_STRING = '([^\s]+?)(?:\s|(?<!\\\\)"|(?<!\\\\)\'|$)';
    const REGEX_QUOTED_STRING = '(?:"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\')';

    const ERROR_TOO_MANY_ARGS = 'too_many_args';
    const ERROR_EXPECTED_NO_ARG = 'expected_no_arg';


    const MESSAGE_TYPE = CmdMessage::class;

    protected $arguments = [];

    protected $tokens = [];

    /**
     * @var CommandDefinition
     */
    protected $definition;

    protected $parsed = [];

    /**
     * @var string
     */
    protected $commandName;

    /**
     * @var Collection
     */
    protected $entities;

    /**
     * @var MessageBag
     */
    protected $errors;

    /**
     * @var Message
     */
    protected $origin;

    public function __construct(
        string $input,
        Message $message,
        CommandDefinition $definition
    )
    {
        $this->origin = $message;
        $this->definition = $definition;
        $this->entities = new Collection();
        $this->errors = new MessageBag();
        $this->tokens = $this->tokenize($input);
        $this->commandName = $definition->getCommandName();

        array_shift($this->tokens);
        $this->parse($this->tokens);
        parent::__construct();
    }

    public function getCommandName() : string
    {
        return $this->commandName;
    }

    public function isCorrect() : bool
    {
        return ! $this->errors->any();
    }

    public function getEntities() : array
    {
        return $this->entities->toArray();
    }

    public function getErrors(): array
    {
        return $this->errors->toArray();
    }

    public function isEmpty(): bool
    {
        return $this->entities->isEmpty();
    }

    public function getText(): string
    {
        return $this->toJson();
    }

    public function toMessageData(): array
    {
        return [
            'commandName' => $this->commandName,
            'correct' => $this->isCorrect(),
            'entities' => $this->entities->toArray(),
        ];
    }


    /**
     * Tokenizes a string.
     *
     * @param string $input The input to tokenize
     *
     * @return array An array of tokens
     *
     * @throws InvalidArgumentException When unable to parse input (should never happen)
     */
    protected function tokenize($input)
    {
        $tokens = [];
        $length = \strlen($input);
        $cursor = 0;
        while ($cursor < $length) {
            if (preg_match('/\s+/A', $input, $match, null, $cursor)) {
            } elseif (preg_match('/([^="\'\s]+?)(=?)('.self::REGEX_QUOTED_STRING.'+)/A', $input, $match, null, $cursor)) {
                $tokens[] = $match[1].$match[2].stripcslashes(str_replace(['"\'', '\'"', '\'\'', '""'], '', substr($match[3], 1, \strlen($match[3]) - 2)));
            } elseif (preg_match('/'.self::REGEX_QUOTED_STRING.'/A', $input, $match, null, $cursor)) {
                $tokens[] = stripcslashes(substr($match[0], 1, \strlen($match[0]) - 2));
            } elseif (preg_match('/'.self::REGEX_STRING.'/A', $input, $match, null, $cursor)) {
                $tokens[] = stripcslashes($match[1]);
            } else {
                // should never happen
                throw new InvalidArgumentException(sprintf('Unable to parse input near "... %s ..."', substr($input, $cursor, 10)));
            }

            $cursor += \strlen($match[0]);
        }

        return $tokens;
    }


    /**
     * {@inheritdoc}
     */
    protected function parse(array $tokens)
    {
        $parseOptions = true;
        while (null !== $token = array_shift($tokens)) {
            if ($parseOptions && '' == $token) {
                $this->parseArgument($token);
            } elseif ($parseOptions && '--' == $token) {
                $parseOptions = false;
            } elseif ($parseOptions && 0 === strpos($token, '--')) {
                $this->parseLongOption($token);
            } elseif ($parseOptions && '-' === $token[0] && '-' !== $token) {
                $this->parseShortOption($token);
            } else {
                $this->parseArgument($token);
            }
        }
    }


    /**
     * Parses a short option.
     *
     * @param string $token The current token
     */
    protected function parseShortOption($token)
    {
        $name = substr($token, 1);

        if (\strlen($name) > 1) {
            if ($this->definition->hasShortcut($name[0]) && $this->definition->getOptionForShortcut($name[0])->acceptValue()) {
                // an option with a value (with no space)
                $this->addShortOption($name[0], substr($name, 1));
            } else {
                $this->parseShortOptionSet($name);
            }
        } else {
            $this->addShortOption($name, null);
        }
    }


    /**
     * Parses a short option set.
     *
     * @param string $name The current token
     *
     */
    protected function parseShortOptionSet($name)
    {
        $len = \strlen($name);
        for ($i = 0; $i < $len; ++$i) {

            if (!$this->definition->hasShortcut($name[$i])) {
                $encoding = mb_detect_encoding($name, null, true);
                $shortName = false === $encoding ? $name[$i] : mb_substr($name, $i, 1, $encoding);
                $message = sprintf('The "-%s" option does not exist.', $shortName);
                $this->errors->add($shortName, $message);
                return;
            }

            $option = $this->definition->getOptionForShortcut($name[$i]);
            if ($option->acceptValue()) {
                $this->addLongOption($option->getName(), $i === $len - 1 ? null : substr($name, $i + 1));

                break;
            } else {
                $this->addLongOption($option->getName(), null);
            }
        }
    }


    /**
     * Parses a long option.
     *
     * @param string $token The current token
     */
    protected function parseLongOption($token)
    {
        $name = substr($token, 2);

        if (false !== $pos = strpos($name, '=')) {
            if (0 === \strlen($value = substr($name, $pos + 1))) {
                array_unshift($this->parsed, $value);
            }
            $this->addLongOption(substr($name, 0, $pos), $value);
        } else {
            $this->addLongOption($name, null);
        }
    }


    /**
     * Parses an argument.
     *
     * @param string $token The current token
     *
     */
    protected function parseArgument($token)
    {
        $c = \count($this->arguments);

        // if input is expecting another argument, add it
        if ($this->definition->hasArgument($c)) {
            $arg = $this->definition->getArgument($c);
            $this->arguments[] = $arg->getName();
            $this->entities->put($arg->getName(), $arg->isArray() ? [$token] : $token);

            // if last argument isArray(), append token to last argument
        } elseif ($this->definition->hasArgument($c - 1) && $this->definition->getArgument($c - 1)->isArray()) {
            $arg = $this->definition->getArgument($c - 1);

            $name = $arg->getName();
            $temp = $this->entities->get($name);
            $temp[] = $token;
            $this->entities->put($name, $temp);

            // unexpected argument
        } else {
            $all = $this->definition->getArguments();
            if (\count($all)) {
                $this->errors->add(static::ERROR_TOO_MANY_ARGS, (sprintf('Too many arguments, expected arguments "%s".', implode('" "', array_keys($all)))));
            } else {
                $this->errors->add(static::ERROR_EXPECTED_NO_ARG, sprintf('No arguments expected, got "%s".', $token));
            }
        }
    }


    /**
     * Adds a short option value.
     *
     * @param string $shortcut The short option key
     * @param mixed  $value    The value for the option
     *
     */
    protected function addShortOption($shortcut, $value)
    {
        if (!$this->definition->hasShortcut($shortcut)) {
            $this->errors->add("-$shortcut", sprintf('The "-%s" option does not exist.', $shortcut));
            return;
        }

        $this->addLongOption($this->definition->getOptionForShortcut($shortcut)->getName(), $value);
    }


    /**
     * Adds a long option value.
     *
     * @param string $name  The long option key
     * @param mixed  $value The value for the option
     */
    protected function addLongOption($name, $value)
    {
        if (!$this->definition->hasOption($name)) {
            $this->errors->add("--$name", sprintf('The "--%s" option does not exist.', $name));
            return;
        }

        $option = $this->definition->getOption($name);

        if (null !== $value && !$option->acceptValue()) {
            $this->errors->add("--$name", sprintf('The "--%s" option does not accept a value.', $name));
            return;
        }

        if (\in_array($value, ['', null], true) && $option->acceptValue() && \count($this->parsed)) {
            // if option accepts an optional or mandatory argument
            // let's see if there is one provided
            $next = array_shift($this->parsed);
            if ((isset($next[0]) && '-' !== $next[0]) || \in_array($next, ['', null], true)) {
                $value = $next;
            } else {
                array_unshift($this->parsed, $next);
            }
        }

        if (null === $value) {
            if ($option->isValueRequired()) {
                $this->errors->add("--$name", sprintf('The "--%s" option requires a value.', $name));
                return;
            }

            if (!$option->isArray() && !$option->isValueOptional()) {
                $value = true;
            }
        }

        $optionName = "--$name";
        if ($option->isArray()) {
            $optionVal = $this->entities->pull($optionName, []);
            $optionVal[] = $value;
            $this->entities->put($optionName, $optionVal);
        } else {
            $this->entities->put($optionName, $value);
        }
    }

    public function offsetExists($offset)
    {
        return $this->entities->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->entities->get($offset, null);
    }

    public function offsetSet($offset, $value)
    {
        $this->entities->put($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->entities->forget($offset);
    }

    public function getOriginMessage(): Message
    {
        return $this->origin;
    }


}