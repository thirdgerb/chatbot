<?php

/**
 * Class Bootstrapper
 * @package Commune\Chatbot\Framework\Bootstrap
 */

namespace Commune\Chatbot\Framework\Bootstrap;


use Commune\Chatbot\Blueprint\Application;

/**
 * chatbot 的启动器. 拆出来解耦.
 *
 * Interface Bootstrapper
 * @package Commune\Chatbot\Framework\Bootstrap
 */
interface Bootstrapper
{

    public function bootstrap(Application $app) : void;

}