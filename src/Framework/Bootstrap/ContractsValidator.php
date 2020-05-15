<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Bootstrap;

use Commune\Blueprint\Exceptions\HostBootingException;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Framework\Bootstrapper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ContractsValidator implements Bootstrapper
{
       

    public function bootstrap(App $app): void
    {
        $logInfo = $app->getLogInfo();
        
        $procBindings = $this->getProcBindings();

        $proc = $app->getProcContainer();
        foreach ($procBindings as $abstract) {
            if (!$proc->bound($abstract)) {
                throw new HostBootingException(
                    $logInfo->bootContractNotBound($abstract)
                );
            }
        }

        $reqBindings = $this->getReqBindings();
        $req = $app->getReqContainer();
        foreach ($reqBindings as $abstract) {
            if (!$req->bound($abstract)) {
                throw new HostBootingException(
                    $logInfo->bootContractNotBound($abstract)
                );
            }
        }
    }

    abstract public function getProcBindings() : array;

    abstract public function getReqBindings() : array;

}