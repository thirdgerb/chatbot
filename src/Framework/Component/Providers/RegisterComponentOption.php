<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Component\Providers;

use Commune\Contracts\ServiceProvider;
use Commune\Container\ContainerContract;
use Commune\Support\Registry\OptRegistry;

/**
 * 将 component 的 option 注册到正式的 option 中.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id
 * @property-read string $componentName
 * @property-read string $resourceName
 * @property-read string $optionClass
 * @property-read string $optionName
 */
class RegisterComponentOption extends ServiceProvider
{
    public static function stub(): array
    {
        return [
            'componentName' => '',
            'resourceName' => '',
            'optionClass' => '',
            'optionName' => '',
        ];
    }

    public function getDefaultScope(): string
    {
        return self::SCOPE_PROC;
    }

    public function __get_id() : string
    {
        return static::class
            . ':'
            . $this->componentName
            . ':'
            . $this->resourceName;
    }


    public function boot(ContainerContract $app): void
    {
        $name = LoadComponentOption::makeComponentOptionId(
            $this->componentName,
            $this->resourceName
        );

        /**
         * @var OptRegistry $registry
         */
        $registry = $app->get(OptRegistry::class);

        $componentCategory = $registry->getCategory($name);
        $category = $registry->getCategory($this->optionName);

        // 注册到正式的option 分类中.
        foreach ($componentCategory->eachId() as $id) {
            $option = $componentCategory->find($id);
            $category->save($option, true);
        }
    }

    public function register(ContainerContract $app): void
    {
    }


}