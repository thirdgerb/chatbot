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
     */
    public function __construct(SessionDataIdentity $id)
    {
        parent::__construct($id->type .' id ' . $id->id . ' not found');
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