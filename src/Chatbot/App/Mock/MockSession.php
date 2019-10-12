<?php

namespace Commune\Chatbot\App\Mock;

use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Session\Session;


trait MockSession
{
    /**
     * @var \Mockery\MockInterface|\Mockery\LegacyMockInterface
     */
    protected $fakeSession;

    /**
     * @param string|Message $input
     * @return Session
     */
    protected function createSessionMocker($input) : Session
    {
        $input = $input instanceof Message ? $input : new Text($input);

        $session = \Mockery::mock(Session::class);
        $session->expects('getPossibleIntent')->andReturn(null);

        /**
         * @var \stdClass $incoming
         */
        $incoming = \Mockery::mock(IncomingMessage::class);

        /**
         * @var \stdClass $session
         */
        $session->incomingMessage = $incoming;
        $incoming->message = $input;
        return $this->fakeSession = $session;
    }


}