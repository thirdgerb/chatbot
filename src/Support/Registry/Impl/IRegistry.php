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
use Commune\Support\Registry\Meta\CategoryMeta;
use Commune\Support\Registry\Registry;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRegistry implements Registry
{
    /**
     * @var ContainerInterface
     */
    protected $container;


    /**
     * @var CategoryMeta[]
     */
    protected $categoryMetas = [];

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Category[]
     */
    protected $categories = [];


    /**
     * OptionRepositoryImpl constructor.
     * @param ContainerInterface $container
     * @param LoggerInterface $logger
     */
    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
    }

    public function registerCategory(CategoryMeta $meta): void
    {
        $this->categoryMetas[$meta->name] = $meta;
    }

    public function getCategoryMeta(string $categoryName): CategoryMeta
    {
        $meta = $this->categoryMetas[$categoryName] ?? null;

        if (empty($meta)) {
            throw new CategoryNotFoundException($categoryName);
        }

        return $meta;
    }

    public function hasCategory(string $categoryName): bool
    {
        return array_key_exists($categoryName, $this->categoryMetas);
    }

    public function getCategory(string $categoryName): Category
    {
        return $this->categories[$categoryName]
            ?? $this->categories[$categoryName] = new ICategory(
                $this->container,
                $this->logger,
                $this->categoryMetas[$categoryName]
            );
    }

    public function __destruct()
    {
        $this->container = null;
        $this->logger = null;

        // 否则不能垃圾回收.
        $this->categories = [];
        $this->categoryMetas = [];
    }

}