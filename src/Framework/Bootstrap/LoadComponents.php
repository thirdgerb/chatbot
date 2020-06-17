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

use Commune\Support\Struct\Struct;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Framework\Bootstrapper;
use Commune\Blueprint\Framework\ComponentOption;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;

/**
 * 加载模块的组件.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class LoadComponents implements Bootstrapper
{
    public function bootstrap(App $app): void
    {
        $configs = $this->getComponentConfigs($app);
        $registrar = $app->getServiceRegistry();

        foreach ($configs as $key => $value) {
            $component = $this->wrapComponent($key, $value);
            $registrar->registerComponent($component);
        }
    }

    public function wrapComponent($name, $value) : ComponentOption
    {
        if (
            is_string($value)
            && is_a($value, ComponentOption::class, TRUE)
        ) {
            return call_user_func([$value, Struct::CREATE_FUNC]);
        }

        if (
            is_string($name)
            && is_a($name, ComponentOption::class, TRUE)
            && is_array($value)
        ) {
            return call_user_func([$name, Struct::CREATE_FUNC], $value);
        }

        throw new InvalidArgumentException('invalid component config');
    }

    abstract public function getComponentConfigs(App $app) : array;
}