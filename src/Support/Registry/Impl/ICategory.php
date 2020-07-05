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
use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Option\Option;
use Commune\Support\Registry\Storage;
use Psr\Container\ContainerInterface;
use Commune\Support\Registry\Exceptions\OptionNotFoundException;

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
     * @var Storage|null
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
    }


    public function getConfig() : CategoryOption
    {
        return $this->categoryOption;
    }


    public function boot(bool $initialize = true): void
    {
        if ($this->booted) {
            return;
        }

        // 如果 Driver 需要初始化的话.
        $storage = $this->getStorage();
        $storage->getDriver()->boot(
            $this->categoryOption,
            $storage->getOption()
        );

        $initStorage = $this->getInitialStorage();
        if (isset($initStorage)) {
            $initStorage->getDriver()->boot(
                $this->categoryOption,
                $initStorage->getOption()
            );
        }

        $this->booted = true;

        // 看看是否需要初始化. 如果初始化, 会把 initialize storage 的数据转到 storage
        if (!$initialize) {
            $this->initialize();
        }
    }

    protected function initialize() : void
    {
        $initStorage = $this->getInitialStorage();

        if (!isset($initStorage)) {
            return;
        }

        $storage = $this->getStorage();
        $gen = $initStorage->getDriver()->eachOption(
            $this->categoryOption,
            $initStorage->getOption()
        );


        $driver = $storage->getDriver();
        $storageOption = $storage->getOption();
        foreach ($gen as $option) {
            $driver->save(
                $this->categoryOption,
                $storageOption,
                $option,
                true
            );
        }
    }


    public function getStorage(): Storage
    {
        if (isset($this->storage)) {
            return $this->storage;
        }
        $storageOption = $this->categoryOption->storage->toWrapper();
        return $this->storage = new IStorage(
            $this->container,
            $storageOption
        );
    }

    public function getInitialStorage(): ? Storage
    {
        if (isset($this->initialStorage)) {
            return $this->initialStorage;
        }

        $meta = $this->categoryOption->initialStorage;
        if (!isset($meta)) {
            return null;
        }

        $storageOption = $meta->toWrapper();
        return $this->initialStorage = new IStorage(
            $this->container,
            $storageOption
        );
    }

    public function has(string $optionId): bool
    {
        $storage = $this->getStorage();
        return $storage->getDriver()->has(
            $this->categoryOption,
            $storage->getOption(),
            $optionId
        );
    }


    public function find(string $optionId): Option
    {
        $storage = $this->getStorage();
        $option = $storage->getDriver()->find(
            $this->categoryOption,
            $storage->getOption(),
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
        $storage = $this->getStorage();
        return $storage->getDriver()->save(
            $this->categoryOption,
            $storage->getOption(),
            $option,
            $notExists
        );
    }


    public function delete(string $id, string ...$ids): int
    {
        $storage = $this->getStorage();
        return $storage->getDriver()->delete(
            $this->categoryOption,
            $storage->getOption(),
            $id,
            ...$ids
        );
    }

    public function findByIds(array $ids): array
    {
        $storage = $this->getStorage();
        $options = $storage->getDriver()->findByIds(
            $this->categoryOption,
            $storage->getOption(),
            $ids
        );

        $options = array_filter($options, function($option) {
            return !is_null($option);
        });

        return $options;
    }

    public function count(): int
    {
        $storage = $this->getStorage();
        return $storage->getDriver()
            ->count(
                $this->categoryOption,
                $storage->getOption()
            );
    }


    public function paginate(int $offset = 0, int $limit = 20): array
    {
        $storage = $this->getStorage();
        $ids = $storage->getDriver()
            ->paginateIds(
                $this->categoryOption,
                $storage->getOption(),
                $offset,
                $limit
            );

        return array_map(function($id){
            return $this->find($id);
        }, $ids);
    }


    public function searchIds(string $wildcardId): array
    {
        $storage = $this->getStorage();
        return $storage->getDriver()
            ->searchIds(
                $this->categoryOption,
                $storage->getOption(),
                $wildcardId
            );
    }

    public function searchIdExists(string $wildcardId): int
    {
        $ids = $this->searchIds($wildcardId);
        return count($ids);
    }



    public function paginateId(int $offset = 0, int $limit = 20): array
    {
        $storage = $this->getStorage();
        return $storage->getDriver()->paginateIds(
            $this->categoryOption,
            $storage->getOption(),
            $offset,
            $limit
        );
    }

    public function eachId(): \Generator
    {
        $storage = $this->getStorage();
        $each =  $storage->getDriver()->eachId(
            $this->categoryOption,
            $storage->getOption()
        );

        foreach($each as $id) {
            yield $id;
        }
    }

    public function eachOption(): \Generator
    {
        $storage = $this->getStorage();
        $gen = $storage
            ->getDriver()
            ->eachOption(
                $this->categoryOption,
                $storage->getOption()
            );
        foreach ($gen as $option) {
            yield $option;
        }
    }

    public function flush(): bool
    {
        $storage = $this->getStorage();
        return $storage->getDriver()->flush(
            $this->categoryOption,
            $storage->getOption()
        );
    }

}