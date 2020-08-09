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
use Commune\Framework\Component\Providers\LoadTranslations;
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
        $registrar = $app->getServiceRegistry();
        $registrar->dependComponent(static::class, $componentName, $params);
    }


    /**
     * @param App $app
     * @param string $categoryName
     * @param string $optionClass
     * @param string $resourcePath
     * @param bool $isDir
     * @param string $loader
     * @param bool $force
     */
    public function loadResourceOption(
        App $app,
        string $categoryName,
        string $optionClass,
        string $resourcePath,
        bool $isDir,
        string $loader = FileStorageOption::OPTION_PHP,
        bool $force = false
    ) : void
    {
        $registrar = $app->getServiceRegistry();

        $registrar->registerConfigProvider(new LoadComponentOption([
            'componentName' => static::class,
            'resourcePath' => $resourcePath,
            'optionClass' => $optionClass,
            'loader' => $loader,
            'isDir' => $isDir,
        ]), false);

        $registrar->registerProcProvider(new RegisterComponentOption([
            'categoryName' => $categoryName,
            'componentName' => static::class,
            'optionClass' => $optionClass,
            'force' => $force
        ]), false);
    }


    public function loadTranslation(
        App $app,
        string $langDir,
        bool $intl = true,
        bool $force = false
    ) : void
    {
        $registrar = $app->getServiceRegistry();

        $registrar->registerProcProvider(
            new LoadTranslations([
                'id' => static::class . ':trans',
                'path' =>  $langDir,
                'intl' => $intl,
                'force' => $force,
            ]),
            false
        );
    }
}