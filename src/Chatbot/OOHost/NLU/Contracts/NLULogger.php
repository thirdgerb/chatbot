<?php


namespace Commune\Chatbot\OOHost\NLU\Contracts;


use Commune\Chatbot\OOHost\Session\Session;

/**
 * 记录 NLU 匹配的结果.
 */
interface NLULogger
{
    public function logNLUResult(Session $session);

}