<?php


namespace Commune\Support\OptionRepo\Impl;


use Commune\Support\OptionRepo\Exceptions\InvalidArgException;
use Commune\Support\OptionRepo\Options\CategoryMeta;
use Commune\Support\Struct;
use Commune\Support\OptionRepo\Contracts\OptionRepository;
use Commune\Support\OptionRepo\Contracts\OptionStorage;
use Commune\Support\OptionRepo\Contracts\RootOptionStage;
use Commune\Support\OptionRepo\Exceptions\OptionNotFoundException;
use Commune\Support\OptionRepo\Exceptions\RepositoryMetaNotExistsException;
use Commune\Support\OptionRepo\Options\StorageMeta;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class OptionRepositoryImpl implements OptionRepository
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    
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
        $root = $this->getStorage( $rootMeta);
        return $root->has($meta, $rootMeta, $optionId);
    }

    public function find(
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

            $driver = $this->getStorage( $storageMeta);
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
            $rootDriver = $this->getStorage( $storageMeta);
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

    public function findAllVersions(
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

            $driver = $this->getStorage( $storageMeta);
            $result[$storageMeta->name] = $driver->get($meta,  $storageMeta, $optionId);
        }

        $rootMeta = $meta->getRootStorage();
        $rootDriver = $this->getStorage( $rootMeta);
        $result['rootStorage'] = $rootDriver->get($meta, $rootMeta, $optionId);

        return $result;
    }

    /**
     * @param Option $option
     * @throws InvalidArgException
     */
    protected function checkOption(Option $option) : void
    {
        $name = $option->getIdentityName();
        if (empty($name)) {
            throw new InvalidArgException("savable option to OptionRepository should always have unique id");
        }
    }

    public function save(
        string $category,
        Option $option,
        bool $draft = false
    ): void
    {
        $this->checkOption($option);
        $id = $option->getId();
        if ($this->hasConstants($category, $id)) {
            $this->logger->error(
                __METHOD__
                . " try to update immutable $category instance that id is $id"
            );
            return;
        }

        $meta = $this->getCategoryMeta($category);
        $rootMeta = $meta->getRootStorage();
        $rootDriver = $this->getStorage( $rootMeta);


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
            $this->sync($category, $id);
        }
    }

    protected function getStorage(
        StorageMeta $meta
    ) : OptionStorage
    {
        return $this->container->get($meta->getDriver());
    }

    public function saveBatch(
        string $category,
        bool $draft,
        Option ...$options
    ): void
    {
        $toSave = [];
        foreach ($options as $option) {
            $this->checkOption($option);
            $toSave[] = $option;
        }

        $meta = $this->getCategoryMeta($category);
        $rootMeta = $meta->getRootStorage();
        $rootDriver = $this->getStorage( $rootMeta);
        $rootDriver->save($meta, $rootMeta, ...$options);

        if ($draft) {
            $ids = array_map(function(Option $option){
                return $option->getId();
            }, $options);
            $this->deleteFromPipeline($meta, ...$ids);
        }
    }

    public function syncCategory(
        string $category
    ): void
    {
        $meta = $this->getCategoryMeta($category);
        $pipes = array_reverse(iterator_to_array($meta->getStoragePipeline()));

        foreach ($pipes as $pipe) {
            $driver = $this->getStorage($pipe);
            $driver->flush($meta, $pipe);
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
        string $category,
        string ...$ids
    ): void
    {
        $toDeletes = [];
        foreach ($ids as $id) {
            if ($this->hasConstants($category, $id)) {
                $this->logger->error(
                    __METHOD__
                    . " try to delete immutable $category instance that id is $id"
                );
                continue;
            }
            $toDeletes[] = $id;
        }

        $meta = $this->getCategoryMeta($category);

        // 必须先删除根节点.
        $root = $meta->getRootStorage();
        $driver = $this->getStorage($root);
        $driver->delete($meta, $root, ...$ids);
        $this->deleteFromPipeline($meta, ...$ids);

    }

    protected function deleteFromPipeline(
        CategoryMeta $meta,
        string ...$ids
    ) : void
    {
        // 倒序删除. 从下往上删.
        $pipes = array_reverse(iterator_to_array($meta->getStoragePipeline()));
        foreach ($pipes as $storageMeta) {
            /**
             * @var OptionStorage $driver
             */
            $driver = $this->getStorage( $storageMeta);
            $driver->delete($meta, $storageMeta, ...$ids);
        }
    }

    public function sync(
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
        $this->deleteFromPipeline($meta, $id);

    }

    protected function hasConstants(string $category, string $id)  : bool
    {
        return isset($this->constantOptions[$category][$id]);
    }


    /*-------- multi option manager --------*/

    public function count(
        string $category
    ): int
    {
        $meta = $this->getCategoryMeta($category);
        $root = $meta->getRootStorage();
        /**
         * @var RootOptionStage $driver
         */
        $driver = $this->getStorage( $root);
        return $driver->count($meta, $root);
    }

    public function paginateIdToBrief(
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
        $driver = $this->getStorage( $root);
        return $driver->paginateIdToBrief($meta, $root, $page, $lines);
    }

    public function getAllOptionIds(
        string $category
    ): array
    {
        $meta = $this->getCategoryMeta($category);
        $root = $meta->getRootStorage();
        /**
         * @var RootOptionStage $driver
         */
        $driver = $this->getStorage( $root);
        return $driver->getAllOptionIds($meta, $root);
    }

    public function findOptionsByIds(
        string $category,
        array $ids
    ): array
    {
        $meta = $this->getCategoryMeta($category);
        $root = $meta->getRootStorage();
        /**
         * @var RootOptionStage $driver
         */
        $driver = $this->getStorage( $root);
        return $driver->findOptionsByIds($meta, $root, $ids);
    }

    public function searchInBriefs(
        string $category,
        string $query
    ): array
    {
        $meta = $this->getCategoryMeta($category);
        $root = $meta->getRootStorage();
        /**
         * @var RootOptionStage $driver
         */
        $driver = $this->getStorage( $root);
        return $driver->searchOptionsByQuery($meta, $root, $query);
    }

    public function eachOption(
        string $category
    ): \Generator
    {
        $meta = $this->getCategoryMeta($category);
        $root = $meta->getRootStorage();
        /**
         * @var RootOptionStage $driver
         */
        $driver = $this->getStorage( $root);
        foreach ($driver->eachOption($meta, $root) as $option) {
            yield $option;
        }
    }


}