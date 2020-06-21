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
use Commune\Blueprint\Framework\ServiceRegistry;
use Commune\Contracts\ServiceProvider;
use Commune\Support\Struct\Struct;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class RegisterProviders implements Bootstrapper
{
    public function bootstrap(App $app): void
    {
        $registrar = $app->getServiceRegistry();

        $console = $app->getConsoleLogger();
        $info = $app->getLogInfo();

        $console->debug($info->bootingRegisterProvider($app->getId()));

        foreach ($this->getProviderConfigs($app) as $key => $val) {
            $provider = $this->wrapProvider($key, $val);
            $scope = $provider->getDefaultScope();

            switch($scope) {
                case ServiceProvider::SCOPE_CONFIG :
                    $registrar->registerConfigProvider($provider, false);
                    break;
                case ServiceProvider::SCOPE_PROC :
                    $registrar->registerProcProvider($provider, false);
                    break;
                case ServiceProvider::SCOPE_REQ :
                default:
                    $registrar->registerReqProvider($provider, false);
            }
        }
    }

    public static function registerProviderByConfig(ServiceRegistry $registry, array $configs) : void
    {
        foreach ($configs as $key => $val) {
            $provider = self::wrapProvider($key, $val);
            $scope = $provider->getDefaultScope();

            switch($scope) {
                case ServiceProvider::SCOPE_CONFIG :
                    $registry->registerConfigProvider($provider, false);
                    break;
                case ServiceProvider::SCOPE_PROC :
                    $registry->registerProcProvider($provider, false);
                    break;
                case ServiceProvider::SCOPE_REQ :
                default:
                    $registry->registerReqProvider($provider, false);
            }
        }
    }


    public static function wrapProvider($name, $value) : ServiceProvider
    {
        if (is_string($value) && is_a($value, ServiceProvider::class, TRUE)) {
            return call_user_func([$value, Struct::FUNC_CREATE]);
        }

        if (
            is_string($name)
            && is_a($name, ServiceProvider::class, TRUE)
            && is_array($value)
        ) {
            return call_user_func([$name, Struct::FUNC_CREATE], $value);
        }

        throw new InvalidArgumentException(
            "invalid provider config : "
            . var_export([
                'key' => $name,
                'value' => $value,
            ], true)
        );
    }

    abstract public function getProviderConfigs(App $app) : array;
}
