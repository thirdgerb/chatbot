<?php

/**
 * Class Message
 * @package Commune\Chatbot\Framework\Message
 */

namespace Commune\Chatbot\Framework\Message;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Message
{
    const VERBOSE = 'v';
    const VERY_VERBOSE = 'vv';
    const DEBUG = 'vvv';
    const QUIET = 'quiet';
    const NORMAL = 'normal';

    /**
     * The mapping between human readable verbosity levels and Symfony's OutputInterface.
     *
     * @var array
     */
    const VERBOSITY_MAP = [
        self::VERBOSE => OutputInterface::VERBOSITY_VERBOSE,
        self::VERY_VERBOSE => OutputInterface::VERBOSITY_VERY_VERBOSE,
        self::DEBUG => OutputInterface::VERBOSITY_DEBUG,
        self::QUIET => OutputInterface::VERBOSITY_QUIET,
        self::NORMAL => OutputInterface::VERBOSITY_NORMAL,
    ];


    /**
     * @var string
     */
    protected $verbosity;

    abstract public function getText() : string;

    public function toArray() : array
    {
        return [
            'type' => static::class,
            'text' => $this->getTrimText(),
            'verbose' => $this->getVerbosity()
        ];
    }

    public function __construct(string $verbose = self::NORMAL)
    {
        $this->verbosity = $verbose;
    }

    public function getTrimText() : string
    {
        return trim($this->getText());
    }

    /**
     * @return int
     */
    public function getVerbosity(): int
    {
        return self::VERBOSITY_MAP[$this->verbosity] ?? 0;
    }

    public function getVerbosityName() : string
    {
        return $this->verbosity;
    }


    /**
     * @var InputInterface
     */
    protected $commandInput;

    public function getCommandInput() : InputInterface
    {
        if (!isset($this->commandInput)) {
            $this->commandInput = new StringInput($this->getTrimText());
        }
        return $this->commandInput;
    }


}