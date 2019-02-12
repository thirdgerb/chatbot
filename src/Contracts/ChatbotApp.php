<?php

/**
 * Class Application
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;

use Commune\Chatbot\Framework\Routing\IntentRoute;
use Commune\Chatbot\Framework\Routing\Router;
use Psr\Container\ContainerInterface;

interface ChatbotApp extends ContainerInterface
{

    /**
     * Resolve the given type from the container.
     *
     * @param  string  $abstract
     * @param  array  $parameters
     * @return mixed
     */
    public function make($abstract, array $parameters = []);

    public function getChatbotConfig() : array;

    public function getServerDriver() : ServerDriver;

    public function getExceptionHandler() : ExceptionHandler;

    /*------ configure -------*/

    public function getMissMatchMessage() : string;

    public function getRootContext() : string;

    public function getDirectorMaxTicks() : int;

    public function getRuntimePipes() : array;

    public function getBootstrappers() : array;

    public function getContextConfigs() : array;

    public function getAnalyzerCommands() : array;

    public function getAnalyzerMark() : string;

    public function getIntentDefaultRoute(Router $router): IntentRoute;
}