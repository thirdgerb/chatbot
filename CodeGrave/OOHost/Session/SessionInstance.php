<?php


namespace Commune\Chatbot\OOHost\Session;


interface SessionInstance
{

    /**
     * @param Session $session
     * @return static
     */
    public function toInstance(Session $session) : SessionInstance;

    public function isInstanced() : bool ;

    public function getSession() : Session;

    public function __sleep() : array;

    public function __wakeup() : void;
}