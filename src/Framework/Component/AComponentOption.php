<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Component;

use Commune\Blueprint\Framework\App;
use Commune\Support\Option\AbsOption;
use Commune\Blueprint\Framework\ComponentOption;
use Commune\Framework\Component\Providers\LoadComponentOption;
use Commune\Framework\Component\Providers\RegisterComponentOption;
use Commune\Support\Registry\Storage\FileStorageOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AComponentOption extends AbsOption implements ComponentOption
{


    /**
     * 当前组件依赖另一个组件.
     *
     * @param App $app
     * @param string $componentName
     * @param array $params
     */
    public function dependComponent(
        App $app,
        string $componentName,
        array $params = []
    ) : void
    {
        $registrar = $app->getServiceRegistrar();
        $registrar->dependComponent(static::class, $componentName, $params);
    }


    /**
     * 将组件里的 option 资源读取出来, 并加载到注册表正式的分类中.
     * @param App $app
     * @param string $optionName
     * @param string $optionClass
     * @param string $resourceName
     * @param string $resourcePath
     */
    public function loadResourceOption(
        App $app,
        string $optionName,
        string $optionClass,
        string $resourceName,
        string $resourcePath
    ) : void
    {
        $registrar = $app->getServiceRegistrar();

        $registrar->registerConfigProvider(new LoadComponentOption([
            'componentName' => static::class,
            'resourceName' => $resourceName,
            'resourcePath' => $resourcePath,
            'optionClass' => $optionClass,
            'loader' => FileStorageOption::OPTION_PHP,
        ]), false);

        $registrar->registerProcProvider(new RegisterComponentOption([
            'componentName' => static::class,
            'resourceName' => $resourceName,
            'optionClass' => $optionClass,
            'optionName' => $optionName,
        ]), false);
    }

}