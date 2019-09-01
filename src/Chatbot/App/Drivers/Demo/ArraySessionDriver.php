<?php


namespace Commune\Chatbot\App\Drivers\Demo;



use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\History\Breakpoint;
use Commune\Chatbot\OOHost\History\Yielding;
use Commune\Chatbot\OOHost\Session\Driver;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionData;
use Commune\Chatbot\OOHost\Session\Snapshot;
use Psr\Log\LoggerInterface;

class ArraySessionDriver implements Driver
{
    use RunningSpyTrait;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    protected $traceId;

    protected static $sessionData = [];

    protected static $yielding = [];

    protected static $breakpoints = [];

    protected static $contexts = [];

    protected static $snapshots = [];

    public function __construct(Conversation $conversation)
    {
        $this->logger = $conversation->getLogger();
        $this->traceId = $conversation->getTraceId();
        static::addRunningTrace(
            $this->traceId,
            $this->traceId
        );
    }

    public function saveSessionData(
        Session $session,
        SessionData $sessionData
    ): void
    {
        $type = $sessionData->getSessionDataType();
        $id = $sessionData->getSessionDataId();
        self::$sessionData[$type][$id] = serialize($sessionData);
    }

    /**
     * @param string $id
     * @param string $dataType
     * @return SessionData|null
     */
    public function findSessionData(
        string $id,
        string $dataType = ''
    ): ? SessionData
    {
        if (!isset(self::$sessionData[$dataType][$id])) {
            return null;
        }

        $content = self::$sessionData[$dataType][$id];
        $data = unserialize($content);

        return $data instanceof SessionData && $data->getSessionDataType() === $dataType
            ? $data
            : null;
    }


    public function saveYielding(Session $session, Yielding $yielding): void
    {
        $this->saveSessionData($session, $yielding);
    }

    public function findYielding(string $contextId): ? Yielding
    {
        return $this->findSessionData($contextId, SessionData::YIELDING_TYPE);
    }

    public function saveBreakpoint(Session $session, Breakpoint $breakpoint): void
    {
        $this->saveSessionData($session, $breakpoint);
    }

    public function findBreakpoint(Session $session, string $id): ? Breakpoint
    {
        return $this->findSessionData($id, SessionData::BREAK_POINT);
    }

    public function saveContext(Session $session, Context $context): void
    {
        $this->saveSessionData($session, $context);
    }

    public function findContext(Session $session, string $contextId): ? Context
    {
        return $this->findSessionData($contextId, SessionData::CONTEXT_TYPE);
    }

    public function __destruct()
    {
        self::removeRunningTrace($this->traceId);
    }

    public function saveSnapshot(Snapshot $snapshot, int $expireSeconds = 0): void
    {
        $belongsTo = $snapshot->belongsTo;
        $serialized = serialize($snapshot);
        self::$snapshots[$belongsTo] = $serialized;
    }

    public function findSnapshot(string $belongsTo): ? Snapshot
    {
        $unserialized = self::$snapshots[$belongsTo] ?? null;

        if ($unserialized) {
            return unserialize($unserialized);
        }

        return null;
    }

    public function clearSnapshot(string $belongsTo): void
    {
        unset(self::$snapshots[$belongsTo]);
    }


}