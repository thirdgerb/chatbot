<?php

/**
 * Class Message
 * @package Commune\Chatbot\Framework\Message
 */

namespace Commune\Chatbot\Framework\Message;

use Commune\Chatbot\Framework\Support\ArrayAbleToJson;
use Commune\Chatbot\Framework\Support\ChatbotUtils;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Message implements \JsonSerializable
{
    use ArrayAbleToJson;

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

    abstract public function getData() : array;

    public function toArray() : array
    {
        return [
            'type' => static::class,
            'data' => $this->getData(),
            'verbose' => $this->getVerbosity()
        ];
    }

    public function __construct(string $verbose = self::NORMAL)
    {
        $this->verbosity = $verbose;
    }

    public function getTrimText() : string
    {
        $text = $this->getText();
        $text = ChatbotUtils::sbc2dbc($text);
        return trim($text);
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
}