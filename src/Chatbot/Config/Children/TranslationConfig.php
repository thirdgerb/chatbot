<?php

/**
 * Class TranslationConfig
 * @package Commune\Chatbot\Config\Children
 */

namespace Commune\Chatbot\Config\Children;

use Commune\Support\Option;

/**
 * Class TranslationConfig
 * @package Commune\Chatbot\Config\Children
 *
 * @property-read string $loader
 * @property-read string $resourcesPath
 * @property-read string $defaultLocale
 * @property-read string|null $cacheDir
 *
 */
class TranslationConfig extends Option
{
    public static function stub(): array
    {
        return [
            'loader' => 'php',
            'resourcesPath' => __DIR__ ,
            'defaultLocale' => 'zh',
            'cacheDir' => null
        ];
    }


}