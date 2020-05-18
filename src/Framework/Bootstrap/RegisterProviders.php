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

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Framework\Bootstrapper;
use Commune\Blueprint\Framework\ServiceRegistrar;
use Commune\Contracts\ServiceProvider;
use Commune\Support\Struct\Struct;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class RegisterProviders implements Bootstrapper
{
    public function bootstrap(App $app): void
    {
        $registrar = $app->getServiceRegistrar();

        $console = $app->getConsoleLogger();
        $info = $app->getLogInfo();

        $console->debug($info->bootingRegisterProvider('config providers'));
        foreach ($this->getConfigProviders($app) as $key => $val) {
            $provider = $this->wrapProvider($key, $val);
            $registrar->registerConfigProvider($provider, false);
        }

        $console->debug($info->bootingRegisterProvider('proc providers'));
        foreach ($this->getProcProviders($app) as $key => $val) {
            $provider = $this->wrapProvider($key, $val);
            $registrar->registerProcProvider($provider, false);
        }

        $console->debug($info->bootingRegisterProvider('req providers'));
        foreach ($this->getReqProviders($app) as $key => $val) {
            $provider = $this->wrapProvider($key, $val);
            $registrar->registerReqProvider($provider, false);
        }


    }


    public function wrapProvider($name, $value) : ServiceProvider
    {
        if (is_string($value) && is_a($value, ServiceProvider::class, TRUE)) {
            return call_user_func([$value, Struct::CREATE_FUNC]);
        }

        if (
            is_string($name)
            && is_a($name, ServiceProvider::class, TRUE)
            && is_array($value)
        ) {
            return call_user_func([$name, Struct::CREATE_FUNC], $value);
        }

        throw new InvalidArgumentException('invalid provider config');
    }

    abstract public function getConfigProviders(App $app) : array;

    abstract public function getProcProviders(App $app) : array;

    abstract public function getReqProviders(App $app) : array;
}
