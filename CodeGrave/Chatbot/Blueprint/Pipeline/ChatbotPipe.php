<?php

/**
 * Class ChatbotPipe
 * @package Commune\Chatbot\Blueprint\Pipeline
 */

namespace Commune\Chatbot\Blueprint\Pipeline;


use Commune\Chatbot\Blueprint\Conversation\Conversation;

/**
 * Interface ChatbotPipe
 * @package Commune\Chatbot\Blueprint
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * chatbot 系统中, 用一个一个的管道来处理各种逻辑.
 */
interface ChatbotPipe
{
    public function getPipeName() :string;

    public function handle(Conversation $conversation, \Closure $next) : Conversation;

}