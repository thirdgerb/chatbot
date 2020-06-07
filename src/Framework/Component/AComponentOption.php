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
     * @param App $app
     * @param string $categoryName
     * @param string $optionClass
     * @param string $resourcePath
     * @param bool $isDir
     * @param string $loader
     */
    public function loadResourceOption(
        App $app,
        string $categoryName,
        string $optionClass,
        string $resourcePath,
        bool $isDir,
        string $loader = FileStorageOption::OPTION_PHP
    ) : void
    {
        $registrar = $app->getServiceRegistrar();

        $registrar->registerConfigProvider(new LoadComponentOption([
            'componentName' => static::class,
            'resourcePath' => $resourcePath,
            'optionClass' => $optionClass,
            'loader' => $loader,
            'isDir' => $isDir
        ]), false);

        $registrar->registerProcProvider(new RegisterComponentOption([
            'categoryName' => $categoryName,
            'componentName' => static::class,
            'optionClass' => $optionClass,
        ]), false);
    }

}