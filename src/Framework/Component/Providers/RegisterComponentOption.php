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

use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\ServiceProvider;
use Commune\Container\ContainerContract;
use Commune\Support\Registry\OptRegistry;

/**
 * 将 component 的 option 注册到正式的 option 中.
 * 这些配置会预先读取到
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id                组件的 ID
 * @property-read string $componentName     组件自身的名称.
 * @property-read string $optionClass       配置的类名
 * @property-read string $categoryName      OptRegistry 里的分类名称.
 */
class RegisterComponentOption extends ServiceProvider
{
    const IDENTITY = 'id';

    public static function stub(): array
    {
        return [
            'componentName' => '',
            'optionClass' => '',
            'categoryName' => '',
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
            . $this->optionClass;
    }


    public function boot(ContainerContract $app): void
    {
        $name = LoadComponentOption::makeComponentOptionId(
            $this->componentName,
            $this->optionClass
        );

        /**
         * @var ConsoleLogger $logger
         * @var OptRegistry $registry
         */
        $registry = $app->get(OptRegistry::class);
        $logger = $app->get(ConsoleLogger::class);

        if (!$registry->hasCategory($name)) {
            $logger->debug(
                'skip component option register, target category not exists',
                $this->toArray()
            );
            return;
        }

        $componentCategory = $registry->getCategory($name);
        $category = $registry->getCategory($this->categoryName);

        // 注册到正式的option 分类中.
        $ids = 0;
        foreach ($componentCategory->eachId() as $id) {
            $option = $componentCategory->find($id);
            if ($category->save($option, true)) {
                $ids ++;
            }
        }

        $logger->debug(
            "component option registered : $ids",
            $this->toArray()
        );
    }

    public function register(ContainerContract $app): void
    {
    }


}