<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype\Bootstrap;

use Commune\Framework\Blueprint\App;
use Commune\Framework\Contracts\Bootstrapper;
use Commune\Framework\Contracts\ConsoleLogger;
use Commune\Framework\Contracts\LogInfo;
use Commune\Framework\Exceptions\BootingException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ContractsValidator implements Bootstrapper
{

    /**
     * @var App
     */
    protected $app;

    /**
     * @var ConsoleLogger
     */
    protected $console;

    /**
     * @var LogInfo
     */
    protected $logInfo;

    public function bootstrap(): void
    {
        $procBindings = $this->getProcBindings();

        $proc = $this->app->getProcContainer();
        foreach ($procBindings as $abstract) {
            if (!$proc->bound($abstract)) {
                throw new BootingException(
                   $this->logInfo->bootMissBinding($abstract)
                );
            }
        }

        $reqBindings = $this->getReqBindings();
        $req = $this->app->getReqContainer();
        foreach ($reqBindings as $abstract) {
            if (!$req->bound($abstract)) {
                throw new BootingException(
                    $this->logInfo->bootMissBinding($abstract)
                );
            }
        }
    }

    abstract public function getProcBindings() : array;

    abstract public function getReqBindings() : array;

}