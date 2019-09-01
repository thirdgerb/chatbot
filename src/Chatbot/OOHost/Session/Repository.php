<?php


namespace Commune\Chatbot\OOHost\Session;
use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;

/**
 * @property-read Snapshot $snapshot
 */
class Repository implements RunningSpy
{
    use RunningSpyTrait;

    /**
     * @var Snapshot
     */
    public $snapshot;

    /**
     * @var Session
     */
    public $session;

    /**
     * @var Driver
     */
    public $driver;

    protected $sessionId;

    /**
     * Repository constructor.
     * @param Session $session
     * @param Driver $driver
     * @param Snapshot $snapshot
     */
    public function __construct(
        Session $session,
        Driver $driver,
        Snapshot $snapshot
    )
    {
        $this->session = $session;
        $this->sessionId = $session->sessionId;
        $this->driver = $driver;
        $this->snapshot = $snapshot;

        static::addRunningTrace($this->sessionId, $this->sessionId);
    }


    public function cacheSessionData(SessionData $data) : void
    {
        $this->snapshot->cacheSessionData($data);
    }

    public function fetchSessionData(
        SessionDataIdentity $id,
        \Closure $makeDefault = null
    ) : ? SessionData
    {
        // 数据要么存在snapshot里, 要么存在数据库里
        $data = $this->snapshot->fetchSessionData($id, function() use ($id) {
            switch($id->type) {
                case SessionData::CONTEXT_TYPE :
                    return $this->driver->findContext($this->session, $id->id);
                case SessionData::BREAK_POINT :
                    return $this->driver->findBreakpoint($this->session, $id->id);
                default :
                    return null;
            }
        });

        // 如果都没保存, 则用传入的闭包生成一个新的实例, 并保存.
        if (!isset($data) && isset($makeDefault)) {
            $data = $makeDefault();
            $this->snapshot->cacheSessionData($data);
        }

        // 没有 instance 过, 才会调用 instance
        if ($data instanceof SessionInstance && !$data->isInstanced()) {
            $data->toInstance($this->session);
        }

        return $data;
    }

    public function __get($name)
    {
        if ($name === 'snapshot') {
            return $this->snapshot;
        }
        return null;
    }

    public function __destruct()
    {
        static::removeRunningTrace($this->sessionId);
    }
}