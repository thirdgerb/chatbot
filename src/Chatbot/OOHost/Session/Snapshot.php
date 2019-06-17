<?php


namespace Commune\Chatbot\OOHost\Session;

use Commune\Chatbot\OOHost\History\Breakpoint;

class Snapshot
{
    /**
     * @var string
     */
    public $sessionId;

    /**
     * @var Breakpoint|null
     */
    public $breakpoint;

    /**
     * @var SessionData[][]
     */
    public $cachedSessionData = [];

    /**
     * @var SessionData[][]
     */
    public $savedSessionData = [];


    /**
     * Snapshot constructor.
     * @param string $sessionId
     */
    public function __construct(string $sessionId)
    {
        $this->sessionId = $sessionId;
    }


    public function cacheSessionData(SessionData $data)
    {
        $type = $data->getSessionDataType();
        $id = $data->getSessionDataId();
        $this->cachedSessionData[$type][$id] = $data;
    }

    /**
     * @param SessionDataIdentity $identity
     * @param \Closure|null $finder
     * @return SessionData|null
     */
    public function fetchSessionData(
        SessionDataIdentity $identity,
        \Closure $finder = null
    ) : ? SessionData
    {
        $type = $identity->type;
        $id = $identity->id;

        if (isset($this->cachedSessionData[$type][$id])) {
            return $this->cachedSessionData[$type][$id];
        }

        if (isset($this->savedSessionData[$type][$id])) {
            $data = $this->savedSessionData[$type][$id];
            $this->cacheSessionData($data);
            return $data;
        }

        if (isset($finder)) {
            $data = $finder();
            if ($data instanceof SessionData) {
                $this->cacheSessionData($data);
                return $data;
            }
        }

        return null;
    }

    public function __sleep()
    {
        $this->savedSessionData = $this->cachedSessionData;
        // 不缓存cached. 这样每次反序列化时, cached 为空. 只有上一次被用过的, 才会被快照.
        return ['sessionId', 'savedSessionData', 'breakpoint'];
    }


}