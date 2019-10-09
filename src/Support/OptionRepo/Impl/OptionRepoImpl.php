<?php


namespace Commune\Support\OptionRepo\Impl;


use Commune\Chatbot\Config\Options\OptionRepoMeta;
use Commune\Support\Option;
use Commune\Support\OptionRepo\Contracts\OptionRepo;
use Commune\Support\OptionRepo\Contracts\OptionStorage;
use Commune\Support\OptionRepo\Contracts\RootOptionStage;
use Commune\Support\OptionRepo\Exceptions\OptionNotFoundException;
use Commune\Support\OptionRepo\Exceptions\RepositoryMetaNotExistsException;
use Commune\Support\OptionRepo\Exceptions\SynchroniseFailException;
use Commune\Support\OptionRepo\Options\StorageMeta;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class OptionRepoImpl implements OptionRepo
{
    /**
     * @var OptionRepoMeta[]
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


    public function registerMeta(OptionRepoMeta $meta): void
    {
        $metaId = $meta->getId();
        $this->metas[$metaId] = $meta;
        if (!empty($meta->constants)) {
            foreach ($meta->constants as $constantData) {
                $option = $meta->newOption($constantData);
                $this->constantOptions[$metaId][$option->getId()] = $option;
            }
        }
    }

    public function getMeta(string $optionName): OptionRepoMeta
    {
        $meta = $this->metas[$optionName] ?? null;
        if (empty($meta)) {
            throw new RepositoryMetaNotExistsException($optionName);
        }

        return $meta;
    }

    public function hasMeta(string $optionName): bool
    {
        return isset($this->metas[$optionName]);
    }

    public function has(string $optionName, string $id, ContainerInterface $container): bool
    {
        if ($this->hasConstants($optionName, $id)) {
            return true;
        }

        $meta = $this->getMeta($optionName);
        $rootMeta = $meta->rootStorage;
        $root = $container->get($rootMeta->driver);
        return $root->has($id, $meta);
    }

    public function find(string $optionName, string $id, ContainerInterface $container): Option
    {
        if ($this->hasConstants($optionName, $id)) {
            return $this->constantOptions[$optionName][$id];
        }

        $meta = $this->getMeta($optionName);

        $noDataStack = [];
        $result = null;

        /**
         * @var OptionStorage $driver
         * @var OptionStorage $rootDriver
         */
        foreach ($meta->storagePipeline as $storageMeta) {

            $driverName = $storageMeta->driver;
            $driver = $container->get($driverName);
            $result = $driver->get($optionName, $id, $storageMeta);

            // 数据已经找到, 跳出循环.
            if (isset($result)) {
                break;
            }

            array_push($noDataStack, [$driver, $storageMeta]);
        }

        // 所有管道都没有找到.
        if (!isset($result)) {
            $storageMeta = $meta->rootStorage;
            $rootDriver = $container->get($storageMeta->driver);
            $result = $rootDriver->get($optionName, $id, $storageMeta);
            if (!isset($result)) {
                throw new OptionNotFoundException($optionName, $id);
            }
        }

        if (empty($noDataStack)) {
            return $result;
        }

        // 从前往后存, 这样下一个请求拿到数据更快.
        foreach ($noDataStack as list($driver, $storageMeta )) {
            if ($driver->lockId($optionName, $id, $storageMeta)) {
                $driver->save($result, $storageMeta);
            } else {
                $this->lockDriverFail(__METHOD__, $storageMeta, $optionName, $id);

            }
        }

        return $result;

    }

    public function findEach(string $optionName, string $id, ContainerInterface $container): array
    {
        $meta = $this->getMeta($optionName);

        $result = [];
        foreach ($meta->storagePipeline as $storageMeta) {
            /**
             * @var OptionStorage $driver
             */
            $driver = $container->get($storageMeta->driver);
            $result[$storageMeta->name] = $driver->get($optionName, $id, $storageMeta);
        }

        $rootMeta = $meta->rootStorage;
        $result['rootStorage'] = $container->get($rootMeta->driver)->get($optionName, $id, $rootMeta);

        return $result;
    }

    public function save(Option $option, bool $draft = false, ContainerInterface $container): void
    {
        $optionClazz = get_class($option);
        $id = $option->getId();
        if ($this->hasConstants($optionClazz, $id)) {
            $this->logger->error(
                __METHOD__
                . " try to update immutable $optionClazz instance that id is $id"
            );
            return;
        }

        $meta = $this->getMeta($optionClazz);
        $rootMeta = $meta->rootStorage;
        $rootDriver = $container->get($rootMeta->driver);


        /**
         * @var OptionStorage $driver
         * @var OptionStorage $rootDriver
         */
        if (!$rootDriver->lockId($optionClazz, $id, $rootMeta)) {
            $this->lockDriverFail(__METHOD__, $rootMeta, $optionClazz, $id);
            // 没锁上只是记日志, 理论上别的请求完成了锁的操作.
            return;
        }

        $rootDriver->save($option, $rootMeta);

        // 如果不是打草稿, 就直接同步.
        if (!$draft) {
            $this->sync($optionClazz, $id, $container);
        }

    }

    protected function lockDriverFail(string $method , StorageMeta $meta, string $optionName, string $id) : void
    {
        $this->logger->warning(
            $method
            . " failed to save option $optionName id $id to driver {$meta->driver}, resource is locked"

        );
    }

    public function delete(string $optionName, string $id, ContainerInterface $container): void
    {
        if ($this->hasConstants($optionName, $id)) {
            $this->logger->error(
                __METHOD__
                . " try to delete immutable $optionName instance that id is $id"
            );
            return;
        }

        $meta = $this->getMeta($optionName);

        foreach ($meta->storagePipeline as $storageMeta) {
            /**
             * @var OptionStorage $driver
             */
            $driver = $container->get($storageMeta->driver);
            $driver->delete($optionName, $storageMeta, $id);
        }

        $root = $meta->rootStorage;

        $container->get($root->driver)->forget($optionName, $id, $root);

    }

    public function sync(string $optionName, string $id, ContainerInterface $container): void
    {
        if ($this->hasConstants($optionName, $id)) {
            $this->logger->error(
                __METHOD__
                . " try to sync immutable $optionName instance that id is $id"
            );
            return;
        }

        $meta = $this->getMeta($optionName);
        $rootMeta = $meta->rootStorage;
        $rootDriver = $container->get($rootMeta->driver);

        /**
         * @var OptionStorage $driver
         * @var OptionStorage $rootDriver
         */
        $data = $rootDriver->get($optionName, $id, $rootMeta);
        if (!isset($data)) {
            throw new OptionNotFoundException($optionName, $id);
        }

        $drivers = [];
        foreach ($meta->storagePipeline as $storageMeta) {
            /**
             * @var OptionStorage $driver
             */
            $driver = $container->get($storageMeta->driver);
            if (!$driver->lockId($optionName, $id, $storageMeta)) {
                throw new SynchroniseFailException($optionName, $id, "lock driver {$storageMeta->driver} failed");
            }

            $drivers[] = [$driver, $storageMeta];
        }

        foreach ($drivers as list($driver, $meta)) {
            $driver->save($data, $meta);
        }
    }

    protected function hasConstants(string $optionName, string $id)  : bool
    {
        return isset($this->constantOptions[$optionName][$id]);
    }


    /*-------- multi option manager --------*/

    public function count(string $optionName, ContainerInterface $container): int
    {
        $meta = $this->getMeta($optionName);
        $root = $meta->rootStorage;
        /**
         * @var RootOptionStage $driver
         */
        $driver = $container->get($root->driver);
        return $driver->count($optionName, $root);
    }

    public function paginateIdToBrief(string $optionName, ContainerInterface $container, int $page = 1, int $lines = 20): array
    {
        $meta = $this->getMeta($optionName);
        $root = $meta->rootStorage;
        /**
         * @var RootOptionStage $driver
         */
        $driver = $container->get($root->driver);
        return $driver->paginateIdToBrief($optionName, $root, $page, $lines);
    }

    public function getAllOptionIds(string $optionName, ContainerInterface $container): array
    {
        $meta = $this->getMeta($optionName);
        $root = $meta->rootStorage;
        /**
         * @var RootOptionStage $driver
         */
        $driver = $container->get($root->driver);
        return $driver->getAllOptionIds($optionName, $root);
    }

    public function findOptionsByIds(string $optionName, array $ids, ContainerInterface $container): array
    {
        $meta = $this->getMeta($optionName);
        $root = $meta->rootStorage;
        /**
         * @var RootOptionStage $driver
         */
        $driver = $container->get($root->driver);
        return $driver->findOptionsByIds($optionName, $ids, $root);
    }

    public function searchInBriefs(string $optionName, string $query, ContainerInterface $container): array
    {
        $meta = $this->getMeta($optionName);
        $root = $meta->rootStorage;
        /**
         * @var RootOptionStage $driver
         */
        $driver = $container->get($root->driver);
        return $driver->searchOptionsByQuery($optionName, $query, $root);
    }


}