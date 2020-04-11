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
use Commune\Support\Registry\Exceptions\StructNotFoundException;
use Commune\Support\Registry\Meta\CategoryMeta;
use Commune\Support\Struct\Struct;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ICategory implements Category
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CategoryMeta
     */
    protected $categoryMeta;

    /**
     * ICategory constructor.
     * @param ContainerInterface $container
     * @param LoggerInterface $logger
     * @param CategoryMeta $categoryMeta
     */
    public function __construct(ContainerInterface $container, LoggerInterface $logger, CategoryMeta $categoryMeta)
    {
        $this->container = $container;
        $this->logger = $logger;
        $this->categoryMeta = $categoryMeta;
    }

    public function has(string $optionId): bool
    {
        // TODO: Implement has() method.
    }

    public function find(string $optionId): Struct
    {
        // TODO: Implement find() method.
    }

    public function getStorageVersions(string $optionId): array
    {
        // TODO: Implement getStorageVersions() method.
    }

    public function save(Struct $struct, bool $draft = false): void
    {
        // TODO: Implement save() method.
    }

    public function saveBatch(
        bool $draft,
        Struct ...$structs
    ): void
    {
        // TODO: Implement saveBatch() method.
    }

    public function syncCategory(bool $rootToTop): void
    {
        // TODO: Implement syncCategory() method.
    }

    public function syncOption(string $id): void
    {
        // TODO: Implement syncOption() method.
    }

    public function delete(string ...$ids): void
    {
        // TODO: Implement delete() method.
    }

    public function count(): int
    {
        // TODO: Implement count() method.
    }

    public function getAllIds(): array
    {
        // TODO: Implement getAllIds() method.
    }

    public function paginate(string $query = '', int $offset = 0, int $lines = 20): array
    {
        // TODO: Implement paginate() method.
    }

    public function findByIds(array $ids): array
    {
        // TODO: Implement findByIds() method.
    }

    public function each(): \Generator
    {
        // TODO: Implement each() method.
    }


}