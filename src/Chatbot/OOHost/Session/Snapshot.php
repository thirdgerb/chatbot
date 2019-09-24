<?php


namespace Commune\Chatbot\OOHost\Session;

use Commune\Chatbot\OOHost\History\Breakpoint;

class Snapshot implements \Serializable
{
    /**
     * @var string
     */
    public $sessionId;

    /**
     * @var string
     */
    public $belongsTo;

    /**
     * @var Breakpoint|null
     */
    public $breakpoint;

    /**
     * 如果没有saved, 则可能出错了.
     * @var bool
     */
    public $saved = false;

    /**
     * Snapshot constructor.
     * @param string $sessionId
     * @param string $belongsTo
     */
    public function __construct(string $sessionId, string $belongsTo)
    {
        $this->belongsTo = $belongsTo;
        $this->sessionId = $sessionId;
    }

    public function serialize()
    {
        $data = [
            'sessionId' => $this->sessionId,
            'belongsTo' => $this->belongsTo,
            'saved' => $this->saved,
            'breakpoint' => serialize($this->breakpoint),
        ];
        return json_encode($data);
    }

    public function unserialize($serialized)
    {
        $data = json_decode($serialized);
        $this->sessionId = $data->sessionId;
        $this->belongsTo = $data->belongsTo;
        $this->saved = $data->saved;
        $this->breakpoint = unserialize($data->breakpoint);
    }


    public function __sleep()
    {
        //$this->savedSessionData = $this->cachedSessionData;
        // 不缓存cached. 这样每次反序列化时, cached 为空. 只有上一次被用过的, 才会被快照.
        return ['sessionId', 'belongsTo', 'breakpoint', 'saved'];
    }


}