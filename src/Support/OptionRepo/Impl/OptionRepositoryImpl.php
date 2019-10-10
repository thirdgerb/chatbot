<?php


namespace Commune\Support\OptionRepo\Impl;


use Commune\Support\OptionRepo\Exceptions\InvalidArgException;
use Commune\Support\OptionRepo\Options\CategoryMeta;
use Commune\Support\Option;
use Commune\Support\OptionRepo\Contracts\OptionRepository;
use Commune\Support\OptionRepo\Contracts\OptionStorage;
use Commune\Support\OptionRepo\Contracts\RootOptionStage;
use Commune\Support\OptionRepo\Exceptions\OptionNotFoundException;
use Commune\Support\OptionRepo\Exceptions\RepositoryMetaNotExistsException;
use Commune\Support\OptionRepo\Exceptions\SynchroniseFailException;
use Commune\Support\OptionRepo\Options\StorageMeta;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class OptionRepositoryImpl implements OptionRepository
{
    /**
     * @var CategoryMeta[]
     */
    protected $metas = [];

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Option[][]
     */
    protected $constantOptions = [];

    /**
     * OptionRepoImpl constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    public function registerCategory(CategoryMeta $meta): void
    {
        $category = $meta->getId();
        $this->metas[$category] = $meta;
        if (!empty($meta->constants)) {
            foreach ($meta->constants as $constantData) {
                $option = $meta->newOption($constantData);
                $this->constantOptions[$category][$option->getId()] = $option;
            }
        }
    }

    public function getCategoryMeta(string $category): CategoryMeta
    {
        $meta = $this->metas[$category] ?? null;
        if (empty($meta)) {
            throw new RepositoryMetaNotExistsException($category);
        }

        return $meta;
    }

    public function hasCategory(string $category): bool
    {
        return isset($this->metas[$category]);
    }


    public function has(
        ContainerInterface $container,
        string $category,
        string $optionId
    ): bool
    {
        if ($this->hasConstants($category, $optionId)) {
            return true;
        }

        $meta = $this->getCategoryMeta($category);
        $rootMeta = $meta->getRootStorage();
        /**
         * @var RootOptionStage $root
         */
        $root = $container->get($rootMeta->getDriver());
        return $root->has($meta, $rootMeta, $optionId);
    }

    public function find(
        ContainerInterface $container,
        string $category,
        string $optionId
    ): Option
    {
        if ($this->hasConstants($category, $optionId)) {
            return $this->constantOptions[$category][$optionId];
        }

        $meta = $this->getCategoryMeta($category);

        $noDataStack = [];
        $result = null;

        /**
         * @var OptionStorage $driver
         * @var OptionStorage $rootDriver
         */
        foreach ($meta->getStoragePipeline() as $storageMeta) {

            $driverName = $storageMeta->getDriver();
            $driver = $container->get($driverName);
            $result = $driver->get($meta, $storageMeta, $optionId);

            // 数据已经找到, 跳出循环.
            if (isset($result)) {
                break;
            }

            array_push($noDataStack, [$driver, $storageMeta]);
        }

        // 所有管道都没有找到.
        if (!isset($result)) {
            $storageMeta = $meta->getRootStorage();
            $rootDriver = $container->get($storageMeta->getDriver());
            $result = $rootDriver->get($meta, $storageMeta, $optionId);
            if (!isset($result)) {
                throw new OptionNotFoundException($category, $optionId);
            }
        }

        if (empty($noDataStack)) {
            return $result;
        }

        // 从前往后存, 这样下一个请求拿到数据更快.
        foreach ($noDataStack as list($driver, $storageMeta )) {
            if ($driver->lockId($meta, $storageMeta, $optionId)) {
                $driver->save($meta, $storageMeta, $result);
            } else {
                $this->lockDriverFail(__METHOD__, $storageMeta, $category, $optionId);

            }
        }

        return $result;

    }

    public function findEach(
        ContainerInterface $container,
        string $category,
        string $optionId
    ): array
    {
        if ($this->hasConstants($category, $optionId)) {
            return [
                'constants' => $this->constantOptions[$category][$optionId],
            ];
        }

        $meta = $this->getCategoryMeta($category);

        $result = [];
        /**
         * @var OptionStorage $driver
         * @var OptionStorage $rootDriver
         */
        foreach ($meta->getStoragePipeline() as $storageMeta) {

            $driver = $container->get($storageMeta->getDriver());
            $result[$storageMeta->name] = $driver->get($meta,  $storageMeta, $optionId);
        }

        $rootMeta = $meta->getRootStorage();
        $rootDriver = $container->get($rootMeta->getDriver());
        $result['rootStorage'] = $rootDriver->get($meta, $rootMeta, $optionId);

        return $result;
    }

    public function save(
        ContainerInterface $container,
        string $category,
        Option $option,
        bool $draft = false
    ): void
    {
        $id = $option->getId();
        if (empty($id)) {
            throw new InvalidArgException("savable option to OptionRepository should always have unique id");
        }

        if ($this->hasConstants($category, $id)) {
            $this->logger->error(
                __METHOD__
                . " try to update immutable $category instance that id is $id"
            );
            return;
        }

        $meta = $this->getCategoryMeta($category);
        $rootMeta = $meta->getRootStorage();
        $rootDriver = $container->get($rootMeta->getDriver());


        /**
         * @var OptionStorage $driver
         * @var OptionStorage $rootDriver
         */
        if (!$rootDriver->lockId($meta, $rootMeta, $id)) {
            $this->lockDriverFail(__METHOD__, $rootMeta, $category, $id);
            // 没锁上只是记日志, 理论上别的请求完成了锁的操作.
            return;
        }

        $rootDriver->save($meta, $rootMeta, $option);

        // 如果不是打草稿, 就直接同步.
        if (!$draft) {
            $this->sync( $container, $category, $id);
        }

    }

    protected function lockDriverFail(string $method , StorageMeta $storage, string $category, string $id) : void
    {
        $this->logger->warning(
            $method
            . " failed to save option to category $category id $id to driver {$storage->getDriver()}, resource is locked"

        );
    }

    public function delete(
        ContainerInterface $container,
        string $category,
        string $id
    ): void
    {
        if ($this->hasConstants($category, $id)) {
            $this->logger->error(
                __METHOD__
                . " try to delete immutable $category instance that id is $id"
            );
            return;
        }

        $meta = $this->getCategoryMeta($category);

        foreach ($meta->getStoragePipeline() as $storageMeta) {
            /**
             * @var OptionStorage $driver
             */
            $driver = $container->get($storageMeta->getDriver());
            $driver->delete($meta, $storageMeta, $id);
        }

        $root = $meta->getRootStorage();

        $driver = $container->get($root->getDriver());
        $driver->delete($meta, $root, $id);

    }

    public function sync(
        ContainerInterface $container,
        string $category,
        string $id
    ): void
    {
        if ($this->hasConstants($category, $id)) {
            $this->logger->error(
                __METHOD__
                . " try to sync immutable $category instance that id is $id"
            );
            return;
        }

        $meta = $this->getCategoryMeta($category);
        $rootMeta = $meta->getRootStorage();
        $rootDriver = $container->get($rootMeta->getDriver());

        /**
         * @var OptionStorage $driver
         * @var OptionStorage $rootDriver
         */
        $data = $rootDriver->get($meta, $rootMeta, $id);
        if (!isset($data)) {
            throw new OptionNotFoundException($category, $id);
        }

        $drivers = [];
        foreach ($meta->getStoragePipeline() as $storageMeta) {
            /**
             * @var OptionStorage $driver
             */
            $driver = $container->get($storageMeta->getDriver());
            if (!$driver->lockId($meta, $storageMeta, $id)) {
                throw new SynchroniseFailException($category, $id, "lock driver {$storageMeta->getDriver()} failed");
            }

            $drivers[] = [$driver, $storageMeta];
        }

        foreach ($drivers as list($driver, $storageMeta)) {
            $driver->save($meta, $storageMeta, $data);
        }
    }

    protected function hasConstants(string $category, string $id)  : bool
    {
        return isset($this->constantOptions[$category][$id]);
    }


    /*-------- multi option manager --------*/

    public function count(
        ContainerInterface $container,
        string $category
    ): int
    {
        $meta = $this->getCategoryMeta($category);
        $root = $meta->getRootStorage();
        /**
         * @var RootOptionStage $driver
         */
        $driver = $container->get($root->getDriver());
        return $driver->count($meta, $root);
    }

    public function paginateIdToBrief(
        ContainerInterface $container,
        string $category,
        int $page = 1,
        int $lines = 20
    ): array
    {
        $meta = $this->getCategoryMeta($category);
        $root = $meta->getRootStorage();
        /**
         * @var RootOptionStage $driver
         */
        $driver = $container->get($root->getDriver());
        return $driver->paginateIdToBrief($meta, $root, $page, $lines);
    }

    public function getAllOptionIds(
        ContainerInterface $container,
        string $category
    ): array
    {
        $meta = $this->getCategoryMeta($category);
        $root = $meta->getRootStorage();
        /**
         * @var RootOptionStage $driver
         */
        $driver = $container->get($root->getDriver());
        return $driver->getAllOptionIds($meta, $root);
    }

    public function findOptionsByIds(
        ContainerInterface $container,
        string $category,
        array $ids
    ): array
    {
        $meta = $this->getCategoryMeta($category);
        $root = $meta->getRootStorage();
        /**
         * @var RootOptionStage $driver
         */
        $driver = $container->get($root->getDriver());
        return $driver->findOptionsByIds($meta, $root, $ids);
    }

    public function searchInBriefs(
        ContainerInterface $container,
        string $category,
        string $query
    ): array
    {
        $meta = $this->getCategoryMeta($category);
        $root = $meta->getRootStorage();
        /**
         * @var RootOptionStage $driver
         */
        $driver = $container->get($root->getDriver());
        return $driver->searchOptionsByQuery($meta, $root, $query);
    }


}