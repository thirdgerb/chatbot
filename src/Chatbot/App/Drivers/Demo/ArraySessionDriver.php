<?php


namespace Commune\Chatbot\App\Drivers\Demo;



use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\History\Breakpoint;
use Commune\Chatbot\OOHost\History\Yielding;
use Commune\Chatbot\OOHost\Session\Driver;
use Commune\Chatbot\OOHost\Session\Session;
use Psr\Log\LoggerInterface;

class ArraySessionDriver implements Driver
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected static $yielding = [];

    protected static $breakpoints = [];

    protected static $contexts = [];

    /**
     * ArraySessionDriver constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    public function saveYielding(Session $session, Yielding $yielding): void
    {
        self::$yielding[$yielding->contextId] = serialize($yielding);
    }

    public function findYielding(string $contextId): ? Yielding
    {
        if (!isset(self::$yielding[$contextId])) {
            return null;
        }
        $y = unserialize(self::$yielding[$contextId]);
        if ($y instanceof Yielding) {
            return $y;
        }
        return null;
    }

    public function saveBreakpoint(Session $session, Breakpoint $breakpoint): void
    {
        self::$breakpoints[$breakpoint->getSessionDataId()] = serialize($breakpoint);
    }

    public function findBreakpoint(Session $session, string $id): ? Breakpoint
    {
        if (!isset(self::$breakpoints[$id])) {
            return null;
        }

        $s = self::$breakpoints[$id];
        $breakpoint = unserialize($s);
        if ($breakpoint instanceof Breakpoint) {
            return $breakpoint;
        }
        return null;
    }

    public function saveContext(Session $session, Context $context): void
    {
        self::$contexts[$context->getId()] = serialize($context);
    }

    public function findContext(Session $session, string $contextId): ? Context
    {
        if (!isset(self::$contexts[$contextId])) {
            return null;
        }
        $s = self::$contexts[$contextId];
        $context = unserialize($s);
        if ($context instanceof Context) {
            return $context;
        }
        return null;
    }

    public function __destruct()
    {
        $this->logger->debug(__METHOD__);
    }


}