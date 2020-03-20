<?php


namespace Commune\Chatbot\OOHost\Session;


class SessionDataIdentity
{

    /**
     * @var string
     */
    public $id;


    /**
     * @var string
     */
    public $type;

    /**
     * SessionDataIdentity constructor.
     * @param string $sessionDataId
     * @param string $sessionDataType
     */
    public function __construct(string $sessionDataId, string $sessionDataType)
    {
        $this->id = $sessionDataId;
        $this->type = $sessionDataType;
    }


}