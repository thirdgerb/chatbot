<?php


namespace Commune\Chatbot\OOHost\Session;


/**
 * Session 的管道组件.
 *
 * Interface SessionPipe
 * @package Commune\Chatbot\Host\Session
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface SessionPipe
{
    public function handle(Session $session, \Closure $next) : Session;

}