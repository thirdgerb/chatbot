<?php


namespace Commune\Chatbot\App\Components\Configurable\Actions;


use Closure;
use Commune\Chatbot\App\Components\Configurable\Configs\ActionConfig;
use Commune\Chatbot\App\Components\Configurable\Configs\DomainConfig;
use Commune\Chatbot\App\Contexts\RouteDef;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class Factory
{

    public static function make(DomainConfig $domain, ActionConfig $config) : callable
    {
        $name = $config->act;
        if (!method_exists(static::class, $name)) {
            throw new ConfigureException(
                __METHOD__
                . ' action '.$config->act
                . ' not allowed'
            );
        }

        return call_user_func([static::class, $name], $config->value, $domain);
    }

    public static function info($value, DomainConfig $domain) : Closure
    {
        return static::say($value, 'info');
    }

    public static function warning($value, DomainConfig $domain) : Closure
    {
        return static::say($value, 'warning');
    }

    public static function error($value, DomainConfig $domain) : Closure
    {
        return static::say($value, 'error');
    }

    public static function route($value, DomainConfig $domain) : Closure
    {
        if (!is_array($value)) {
            static::typeNotAllowed(__METHOD__, 'array', $value);
        }

        $routes = array_values($value);

        return function (Dialog $dialog) use ($routes, $domain): Navigator {
            return $dialog->redirect->replaceTo(
                new RouteDef($routes, $domain->domain)
            );
        };
    }

    public static function temp($value, DomainConfig $domain) : Closure
    {
        if (!is_array($value)) {
            static::typeNotAllowed(__METHOD__, 'array', $value);
        }

        $temps = $domain->getTemplatesMap();

        return function (Context $self, Dialog $dialog)
                use ($value, $temps, $domain) : ? Navigator
        {
            foreach ($value as $tempName) {
                if (!isset($temps[$tempName])) {
                    continue;
                }

                $actions = $temps[$tempName];

                foreach ($actions as $action) {
                    $caller = Factory::make($domain, $action);

                    $navigator = $dialog->app
                        ->callContextInterceptor($self, $caller);
                    if (isset($navigator)) {
                        return $navigator;
                    }
                }

            }

            return null;
        };
    }



    public static function say($value, string $level = 'info')  : Closure
    {
        if (!is_string($value)) {
            static::typeNotAllowed(__METHOD__, 'string', $value);
        }

        return function(Context $self, Dialog $dialog) use ($level, $value){
            $dialog->say()
                ->withContext($self)
                ->{$level}($value);
            return null;
        };
    }

    protected static function typeNotAllowed(string $method, string $type, $value)
    {
        $given = gettype($value);
        throw new ConfigureException("$method only accept $type, $given given");
    }

}