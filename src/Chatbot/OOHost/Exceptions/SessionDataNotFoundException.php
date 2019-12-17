<?php


namespace Commune\Chatbot\OOHost\Exceptions;

use Commune\Chatbot\Framework\Exceptions\CloseSessionException;
use Commune\Chatbot\OOHost\Session\SessionDataIdentity;

/**
 * 关键数据没有找到, 终止对话.
 */
class SessionDataNotFoundException extends CloseSessionException
{
    /**
     * @var string
     */
    protected $dataType;

    /**
     * @var string
     */
    protected $dataId;

    /**
     * DataNotFoundException constructor.
     * @param SessionDataIdentity $id
     * @param string|null $type
     */
    public function __construct(SessionDataIdentity $id, string $type = null)
    {
        $type = $type ?? $id->type;
        parent::__construct($type .' id ' . $id->id . ' not found');
    }

    /**
     * @return string
     */
    public function getDataType(): string
    {
        return $this->dataType;
    }

    /**
     * @return string
     */
    public function getDataId(): string
    {
        return $this->dataId;
    }

}