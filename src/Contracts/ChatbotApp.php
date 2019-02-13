<?php

/**
 * Class Application
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;

use Commune\Chatbot\Framework\Character\User;
use Commune\Chatbot\Framework\Routing\IntentRoute;
use Commune\Chatbot\Framework\Routing\Router;
use Psr\Container\ContainerInterface;

interface ChatbotApp extends ContainerInterface
{
    /*----- config name const -----*/

    /*----- config.messages -----*/

    const MESSAGES_MISSMATCH = 'messages.miss_match_message';

    const MESSAGES_EXCEPTIONS = 'messages.exceptions';

    /*----- config.context -----*/

    const CONTEXT_ROOT = 'contexts.root';
    const CONTEXT_PRELOAD = 'contexts.preload';

    /*----- config.runtime -----*/

    const RUNTIME_MAX_DIRECT = 'runtime.direct_max_ticks';
    const RUNTIME_PIPES = 'runtime.pipes';
    const RUNTIME_BOOTSTRAPPERS = 'runtime.bootstrappers';
    const RUNTIME_ANALYZERS = 'runtime.analyzers';
    const RUNTIME_COMMAND_MARK = 'runtime.command_mark';

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

    public function isSupervisor(User $sender) : bool;

    /*------ configure -------*/

    /**
     * @param string $configConstantName
     * @param mixed $default
     * @return mixed
     */
    public function getConfig(string $configConstantName, $default = null);

    /*------ intent -------*/

    public function getIntentDefaultRoute(Router $router): IntentRoute;


}