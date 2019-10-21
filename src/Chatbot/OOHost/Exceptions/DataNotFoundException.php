<?php


namespace Commune\Chatbot\OOHost\Exceptions;

use Commune\Chatbot\Framework\Exceptions\LogicException;
use Commune\Chatbot\OOHost\Session\SessionDataIdentity;

class DataNotFoundException extends LogicException
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