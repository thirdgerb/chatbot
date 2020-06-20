<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Registry\Impl;

use Commune\Support\Registry\Category;
use Commune\Support\Registry\Exceptions\CategoryNotFoundException;
use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Registry\OptRegistry;
use Psr\Container\ContainerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IOptRegistry implements OptRegistry
{
    /**
     * @var ContainerInterface
     */
    protected $container;


    /**
     * @var CategoryOption[]
     */
    protected $categoryOptions = [];

    /**
     * @var Category[]
     */
    protected $categories = [];

    /**
     * OptionRepositoryImpl constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function registerCategory(CategoryOption $meta, bool $initialize = false): void
    {
        $metaId = $meta->getId();
        $this->categoryOptions[$metaId] = $meta;
        // 初始化.
        $this->getCategory($metaId)->boot($initialize);
    }

    public function getCategoryOption(string $categoryName): CategoryOption
    {
        $meta = $this->categoryOptions[$categoryName] ?? null;

        if (empty($meta)) {
            throw new CategoryNotFoundException($categoryName);
        }

        return $meta;
    }

    public function hasCategory(string $categoryName): bool
    {
        return array_key_exists($categoryName, $this->categoryOptions);
    }

    public function getCategory(string $categoryName): Category
    {
        if (isset($this->categories[$categoryName])) {
            return $this->categories[$categoryName];
        }

        $option = $this->getCategoryOption($categoryName);
        return $this->categories[$categoryName] = new ICategory($this->container, $option);
    }

    public function eachCategory(): \Generator
    {
        foreach ($this->categoryOptions as $option) {
            yield $this->getCategory($option->getId());
        }
    }

    public function count(): int
    {
        return count($this->categoryOptions);
    }

    public function getCategoryOptions(): array
    {
        return $this->categoryOptions;
    }

    public function __destruct()
    {
        unset($this->container);
        unset($this->categories);
        unset($this->categoryOptions);
    }

}