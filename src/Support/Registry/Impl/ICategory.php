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
use Commune\Support\Registry\Exceptions\OptionNotFoundException;
use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Option\Option;
use Commune\Support\Registry\Meta\StorageOption;
use Commune\Support\Registry\Storage;
use Commune\Support\Utils\StringUtils;
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
     * @var CategoryOption
     */
    protected $categoryOption;

    /*------- cached -------*/

    protected $booted = false;

    /**
     * @var Option[]
     */
    protected $cachedOptions = [];

    /**
     * @var bool[]|null
     */
    protected $allIds;

    /**
     * @var int
     */
    protected $expireAt = 0;


    /**
     * @var int
     */
    protected $cacheExpireAfter;

    /**
     * @var Storage
     */
    protected $storage;

    /**
     * @var StorageOption
     */
    protected $storageOption;

    /**
     * @var Storage
     */
    protected $initialStorage;

    /**
     * ICategory constructor.
     * @param ContainerInterface $container
     * @param LoggerInterface $logger
     * @param CategoryOption $categoryOption
     */
    public function __construct(
        ContainerInterface $container,
        LoggerInterface $logger,
        CategoryOption $categoryOption
    )
    {
        $this->container = $container;
        $this->logger = $logger;
        $this->categoryOption = $categoryOption;
        $this->storageOption = $categoryOption->storage->getWrapper();
        $this->cacheExpireAfter = $categoryOption->cacheExpire;
    }

    protected function checkExpire() : void
    {
        $now = time();
        // 清空所有缓存.
        if ($now >= $this->expireAt) {
            $this->cachedOptions = [];
            $this->allIds = null;
        }

        $this->expireAt = ($now + $this->cacheExpireAfter) - ($now  % $this->cacheExpireAfter);
    }

    public function has(string $optionId): bool
    {
        $ids = $this->getAllIds();
        return array_key_exists($optionId, $ids);
    }

    public function find(string $optionId): Option
    {
        $this->checkExpire();
        if (isset($this->cachedOptions[$optionId])) {
            return $this->cachedOptions[$optionId];
        }

        $option = $this->getStorage()->find(
            $this->categoryOption,
            $this->storageOption,
            $optionId
        );

        if (empty($option)) {
            throw new OptionNotFoundException(
                __METHOD__,
                $optionId
            );
        }

        return $this->cachedOptions[$optionId] = $option;
    }

    public function save(Option $option, bool $notExists = false): bool
    {
        $this->checkExpire();
        $saved = $this->getStorage()->save(
            $this->categoryOption,
            $this->storageOption,
            $option,
            $notExists
        );

        if ($saved) {
            $id = $option->getId();
            $this->cachedOptions[$id] = $option;
            $this->allIds[$id] = true;
        }

        return $saved;
    }


    public function delete(string $id, string ...$ids): int
    {
        $this->checkExpire();

        $deleted = $this->getStorage()->delete(
            $this->categoryOption,
            $this->storageOption,
            $id,
            ...$ids
        );

        array_unshift($ids, $id);
        foreach ($ids as $id) {
            unset($this->cachedOptions[$id]);
            unset($this->allIds[$id]);
        }
        return $deleted;
    }

    public function findByIds(array $ids): array
    {
        $this->checkExpire();

        $outputs = [];
        $toFind = [];
        foreach ($ids as $id) {
            if (isset($this->cachedOptions[$id])) {
                $outputs[$id] = $this->cachedOptions[$id];
            } else {
                $toFind[] = $id;
            }
        }

        if (empty($toFind)) {
            return $outputs;
        }

        $options = $this->getStorage()->findByIds(
            $this->categoryOption,
            $this->storageOption,
            $toFind
        );

        $options = array_filter($options, function($option) {
            return !is_null($option);
        });

        $outputs = $options + $outputs;
        $this->cachedOptions = $options + $this->cachedOptions;

        return $outputs;
    }

    public function count(): int
    {
        $ids = $this->getAllIds();
        return count($ids);
    }

    public function getAllIds(): array
    {
        $this->checkExpire();
        return isset($this->allIds)
            ? array_keys($this->allIds)
            : $this->allIds = array_fill_keys(
                $this->getStorage()->getAllIds(
                    $this->categoryOption,
                    $this->storageOption
                ),
                true
            );
    }

    public function paginate(int $offset = 0, int $limit = 20): array
    {
        $ids = $this->getAllIds();
        $ids = array_slice($ids, $offset, $limit);
        return array_map(function($id){
            return $this->find($id);
        }, $ids);
    }


    public function searchIds(string $wildcardId): array
    {
        $ids = $this->getAllIds();

        if (!StringUtils::isWildCardPattern($wildcardId)) {
            return in_array($wildcardId, $ids)
                ? [$wildcardId]
                : [];
        }

        $pattern = StringUtils::wildcardToRegex($wildcardId);

        return array_filter($ids, function($id) use ($pattern) {
            return (bool) preg_match($pattern, $id);
        });
    }

    public function searchIdExists(string $wildcardId): int
    {
        $ids = $this->searchIds($wildcardId);
        return count($ids);
    }

    public function each(): \Generator
    {
        $ids = $this->getAllIds();
        foreach ($ids as $id) {
            yield $this->find($id);
        }
    }

    public function getStorage(): Storage
    {
        if (isset($this->storage)) {
            return $this->storage;
        }

        $storageOption = $this->categoryOption->storage->getWrapper();
        $driver = $storageOption->getDriver();

        return $this->storage = $this->container->get($driver);
    }

    public function getInitialStorage(): ? Storage
    {
        if (isset($this->initialStorage)) {
            return $this->initialStorage;
        }

        $option = $this->categoryOption->initialStorage;
        if (!isset($option)) {
            return null;
        }

        $driver = $option->getWrapper()->getDriver();
        return $this->initialStorage = $this->container->get($driver);
    }

    public function boot(bool $initialize = false): void
    {
        if ($this->booted) {
            return;
        }

        $storage = $this->getStorage();
        $storage->boot($this->categoryOption, $this->storageOption);
        $this->booted = true;

        if (!$initialize) {
            $this->initialize();
        }
    }

    public function initialize() : void
    {
        $storage = $this->getStorage();
        $storage->boot($this->categoryOption, $this->storageOption);

        $initStorage = $this->getInitialStorage();
        if (!isset($initStorage)) {
            $this->booted = true;
            return;
        }

        $initStorageOption = $this->categoryOption->initialStorage->getWrapper();
        $initStorage->boot(
            $this->categoryOption,
            $initStorageOption
        );

        $ids = $initStorage->getAllIds($this->categoryOption, $initStorageOption);

        foreach ($ids as $id) {
            $option = $initStorage->find(
                $this->categoryOption,
                $initStorageOption,
                $id
            );

            $storage->save(
                $this->categoryOption,
                $this->storageOption,
                $option,
                true
            );
        }
    }



    public function __destruct()
    {
        $this->initialStorage = null;
        $this->storage = null;
        $this->categoryOption = null;
        $this->cachedOptions = [];
        $this->allIds = null;
    }
}