<?php


namespace Commune\Chatbot\OOHost\NLU\Predefined;


use Commune\Chatbot\OOHost\NLU\Contracts\NLULogger;
use Commune\Chatbot\OOHost\Session\Session;

/**
 * 用日志来记录nlu的结果.
 */
class SimpleNLULogger implements NLULogger
{

    public function logNLUResult(Session $session)
    {
        $logger = $session->logger;

        $scope = $session->scope->toArray();
        $message = $session->incomingMessage->getMessage();

        $nluResult = $session->nlu->toArray();

        $logger->info(NLULogger::class, [
            'message' => $message->toArray(),
            'nlu' => $nluResult,
            'scope' => $scope
        ]);
    }


}