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
     * @var CategoryOption
     */
    protected $categoryOption;

    /*------- cached -------*/

    protected $booted = false;

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
     * @param CategoryOption $categoryOption
     */
    public function __construct(
        ContainerInterface $container,
        CategoryOption $categoryOption
    )
    {
        $this->container = $container;
        $this->categoryOption = $categoryOption;
        $this->storageOption = $categoryOption->storage->toWrapper();
    }

    public function getConfig() : CategoryOption
    {
        return $this->categoryOption;
    }

    public function has(string $optionId): bool
    {
        return $this->getStorage()->has(
            $this->categoryOption,
            $this->storageOption,
            $optionId
        );
    }

    public function find(string $optionId): Option
    {
        $option = $this->getStorage()->find(
            $this->categoryOption,
            $this->storageOption,
            $optionId
        );

        if (empty($option)) {
            throw new OptionNotFoundException(
                __METHOD__,
                $this->categoryOption->name,
                $optionId
            );
        }

        return $option;
    }

    public function save(Option $option, bool $notExists = false): bool
    {
        return $this->getStorage()->save(
            $this->categoryOption,
            $this->storageOption,
            $option,
            $notExists
        );
    }


    public function delete(string $id, string ...$ids): int
    {
        return $this->getStorage()->delete(
            $this->categoryOption,
            $this->storageOption,
            $id,
            ...$ids
        );
    }

    public function findByIds(array $ids): array
    {
        $options = $this->getStorage()->findByIds(
            $this->categoryOption,
            $this->storageOption,
            $ids
        );

        $options = array_filter($options, function($option) {
            return !is_null($option);
        });

        return $options;
    }

    public function count(): int
    {
        return $this
            ->getStorage()
            ->count(
                $this->categoryOption,
                $this->storageOption
            );
    }


    public function paginate(int $offset = 0, int $limit = 20): array
    {
        $ids = $this
            ->getStorage()
            ->paginateIds(
                $this->categoryOption,
                $this->storageOption,
                $offset,
                $limit
            );

        return array_map(function($id){
            return $this->find($id);
        }, $ids);
    }


    public function searchIds(string $wildcardId): array
    {
        return $this
            ->getStorage()
            ->searchIds(
                $this->categoryOption,
                $this->storageOption,
                $wildcardId
            );
    }

    public function searchIdExists(string $wildcardId): int
    {
        $ids = $this->searchIds($wildcardId);
        return count($ids);
    }

    public function each(): \Generator
    {
        $each = $this->getStorage()->eachId(
            $this->categoryOption,
            $this->storageOption
        );

        foreach ($each as $id) {
            yield $this->find($id);
        }
    }

    public function getStorage(): Storage
    {
        if (isset($this->storage)) {
            return $this->storage;
        }

        $storageOption = $this->categoryOption->storage->toWrapper();
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

        $driver = $option->toWrapper()->getDriver();
        return $this->initialStorage = $this->container->get($driver);
    }

    public function paginateId(int $offset = 0, int $limit = 20): array
    {
        return $this->getStorage()->paginateIds(
            $this->categoryOption,
            $this->storageOption,
            $offset,
            $limit
        );
    }

    public function eachId(): \Generator
    {
        $each =  $this->getStorage()->eachId(
            $this->categoryOption,
            $this->storageOption
        );

        foreach($each as $id) {
            yield $id;
        }
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

    protected function initialize() : void
    {
        $storage = $this->getStorage();
        $storage->boot($this->categoryOption, $this->storageOption);

        $initStorage = $this->getInitialStorage();
        if (!isset($initStorage)) {
            $this->booted = true;
            return;
        }

        $initStorageOption = $this->categoryOption->initialStorage->toWrapper();
        $initStorage->boot(
            $this->categoryOption,
            $initStorageOption
        );

        $gen = $initStorage->eachId($this->categoryOption, $this->storageOption);


        foreach ($gen as $id) {
            $option = $initStorage->find($this->categoryOption, $this->storageOption, $id);
            $storage->save(
                $this->categoryOption,
                $this->storageOption,
                $option,
                true
            );
        }
    }

    public function flush(): bool
    {
        return $this->getStorage()->flush(
            $this->categoryOption,
            $this->storageOption
        );
    }


    public function __destruct()
    {
        $this->initialStorage = null;
        $this->storage = null;
        $this->categoryOption = null;
    }
}