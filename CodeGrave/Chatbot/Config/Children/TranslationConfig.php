<?php

/**
 * Class TranslationConfig
 * @package Commune\Chatbot\Config\Children
 */

namespace Commune\Chatbot\Config\Children;

use Commune\Chatbot\Contracts\Translator;
use Commune\Support\Option;

/**
 * i18n 模块的配置. 底层使用了 symfony translator
 * @see \Commune\Chatbot\Framework\Providers\TranslatorServiceProvider
 *
 * @property-read string $loader 加载机制, 默认有 php, xliff, csv, yaml 等
 * @property-read string $resourcesPath 资源文件所在一级目录, 二级目录是语种
 * @property-read string $defaultLocale 默认的语种
 * @property-read string|null $cacheDir 缓存配置文件的目录.
 *
 * 
 * 举个例子, resourcesPath 如果是 RESOURCE_PATH, 则目录结构如下:
 *
 * + RESOURCE_PATH
 *      +   zh  // 二级目录名就是语种
 *          -   messages.php  // 文件名就是 Domain, 通常只要 messages 就可以.
 *      +   en
 *          -   messages.php
 *
 */
class TranslationConfig extends Option
{
    public static function stub(): array
    {
        return [
            'loader' => Translator::FORMAT_PHP,
            'resourcesPath' => '' ,
            'defaultLocale' => 'zh',
            'cacheDir' => null
        ];
    }


}