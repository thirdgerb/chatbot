<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Registry\Storage;

use Commune\Blueprint\Ghost\MindMeta\SynonymMeta;
use Commune\Support\Option\Option;
use Commune\Support\Registry\Meta\StorageOption;
use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Registry\Storage;
use Commune\Support\Utils\StringUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsFileStorage implements Storage
{

    /*---- 配置, 文件后缀 -----*/

    protected $ext = '';


    /**
     * @var Option[][]
     */
    protected $optionCaches = [];

    /**
     * 已读取的资源.  categoryId => directory
     * @var  string[]
     */
    protected $resources = [];

    /**
     * option 与文件的关系. [categoryId][optionId] => fileName
     * @var string[][]
     */
    protected $optionPath = [];

    /**
     * @var string[][]
     */
    protected $allIds = [];

    /*------ construct ------*/

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * AbsFileStorage constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }



    /*------ methods ------*/


    /**
     * 将配置的值转化为字符串, 用于写配置.
     * @param array $optionData
     * @param FileStorageOption $storageOption
     * @return string
     */
    abstract protected function parseArrayToString(
        array $optionData,
        FileStorageOption $storageOption
    ): string;

    /**
     * 将配置的字符串转化为 option 的值, 用于读配置.
     * @param string $content
     * @return array
     */
    abstract protected function parseStringToArray(string $content): array;

    /**
     * 配置文件是否正确.
     * @param StorageOption $option
     * @return bool
     */
    abstract protected function isValidOption(StorageOption $option) : bool;

    public function boot(
        CategoryOption $categoryOption,
        StorageOption $storageOption
    ): void
    {
        if (!$this->isLoaded($categoryOption)) {
            $this->loadFile($categoryOption, $this->parseOption($storageOption));
        }
    }


    protected function readFromOptionCache(
        CategoryOption $category,
        string $id
    ) : ? Option
    {
        $cateId = $category->getId();
        return $this->optionCaches[$cateId][$id] ?? null;
    }

    protected function readFromFile(
        CategoryOption $category,
        FileStorageOption $option,
        string $id
    ) : ? Option
    {
        $this->loadFile($category, $option);
        return $this->readFromOptionCache($category, $id);
    }

    protected function newOption(
        string $optionClass,
        array $optionArr
    ) : ? Option
    {

        $id = '';
        try {
            $identity = constant($optionClass . '::IDENTITY');
            $id = $optionArr[$identity] ?? '';

            return call_user_func(
                [$optionClass, Option::CREATE_FUNC],
                $optionArr
            );

        } catch (\Exception $e) {

            $this->logger->error(
                'fail to new option  '
                . $optionClass
                . ", which id may be $id"
                . ', error: ' . $e->getMessage()
            );

            return null;
        }
    }

    protected function isLoaded(CategoryOption $categoryOption) : bool
    {
        // 不是目录的话, 检查目标资源是否已经读取过.
        $cateId = $categoryOption->getId();
        return isset($this->resources[$cateId]);
    }

    protected function loadFile(
        CategoryOption $category,
        FileStorageOption $storageOption
    ) : void
    {
        if ($this->isLoaded($category)) {
            return;
        }

        // 如果是目录, 按配置读取全部文件. 每一个文件是一个 option.
        if ($storageOption->isDir) {
            $this->loadDirAllFiles($category, $storageOption);
            return;
        }

        // 不是目录的话, 检查目标资源是否已经读取过.
        $cateId = $category->getId();

        // 如果目标资源没有读取过, 重新读取资源.
        $path = $storageOption->path;

        // 获得资源的数据.
        $data = $this->readSingleFile($path);
        $this->resources[$cateId] = $storageOption->getId();
        if (empty($data)) {
            return;
        }

        $className = $category->optionClass;

        foreach ($data as $optionArr) {
            $option = $this->newOption($className, $optionArr);
            if (isset($option)) {
                $optionId = $option->getId();
                $this->optionCaches[$cateId][$optionId] = $option;
                $this->optionPath[$cateId][$optionId] = $path;
            }
        }
    }


    protected function loadDirAllFiles(
        CategoryOption $category,
        FileStorageOption $meta
    ) : void
    {
        if ($this->isLoaded($category)) {
            return;
        }

        $cateId = $category->getId();
        $this->resources[$cateId] = $meta->getId();

        $finder = new Finder();
        $finder = $finder
            ->in($meta->path)
            ->depth($meta->depth)
            ->name('/\.' . $this->ext . '$/');


        $optionClass = $category->optionClass;
        foreach ($finder as $file) {
            /**
             * @var \SplFileInfo $file
             */
            $filePath = $file->getRealPath();
            $optionArr = $this->readSingleFile($filePath);
            if (isset($optionArr)) {

                $option = $this->newOption($optionClass, $optionArr);
                if (isset($option)) {
                    $optionId = $option->getId();
                    $this->optionCaches[$cateId][$optionId] = $option;
                    $this->optionPath[$cateId][$optionId] = $filePath;
                }
            }
        }
    }


    protected function readSingleFile(string $path) : ? array
    {
        $content = file_get_contents($path);
        if (empty($content)) {
            return null;
        }

        return $this->parseStringToArray($content);
    }



    protected function parseOption(StorageOption $option) : FileStorageOption
    {
        if ($option instanceof FileStorageOption && $this->isValidOption($option)) {
            return $option;
        }

        throw new \InvalidArgumentException(
            static::class
            . ' only accept storage option that is subclass of '
            . FileStorageOption::class
        );
    }

    protected function deleteToFile(
        CategoryOption $category,
        FileStorageOption $storage,
        array $deletes
    ) : void
    {
        // 如果不是目录, 则把存储文件重新存储.
        if (!$storage->isDir) {
            $this->saveToFile($category, $storage);
            return;
        }

        // 如果是目录, 则删除对应配置的文件.
        $cateId = $category->getId();
        foreach ($deletes as $id) {
            $fileName = $this->optionPath[$cateId][$id]
                ?? $this->getFileName($storage, $id);

            unset($this->optionPath[$cateId][$id]);
            @unlink($fileName);
        }
    }

    protected function getFileName(FileStorageOption $meta, string $id = null) : string
    {
        $id = $id ?? '';
        $id = $this->serializeId($id);
        return rtrim($meta->path, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . $id . '.' . $this->ext;
    }

    protected function serializeId(string $id) : string
    {
        return md5($id);
    }

    /**
     * 将整个目录存到一个文件.
     * @param CategoryOption $category
     * @param FileStorageOption $meta
     */
    protected function saveToFile(CategoryOption $category, FileStorageOption $meta) : void
    {
        $data = $this->getCategoryData($category);
        $data = array_values($data);
        $content = $this->parseArrayToString($data, $meta);
        file_put_contents($meta->path, $content);
    }

    protected function getCategoryData(CategoryOption $category) : array
    {
        $data = [];
        if (isset($this->optionCaches[$category->getId()])) {
            foreach ($this->optionCaches[$category->getId()] as $option) {
                $data[$option->getId()] = $option->toArray();
            }
        }
        return $data;
    }


    /**
     * 将option 对应文件, 存到一个目录下.
     * @param CategoryOption $category
     * @param FileStorageOption $meta
     * @param Option $option
     */
    protected function saveToDir(CategoryOption $category, FileStorageOption $meta, Option $option) : void
    {
        $cateId = $category->getId();
        $optionId = $option->getId();

        $path = $this->optionPath[$cateId][$optionId] ?? null;
        $path = $path ?? $this->getFileName($meta, $optionId);

        $content = $this->parseArrayToString($option->toArray(), $meta);

        file_put_contents($path, $content);
        $this->optionPath[$cateId][$optionId] = $path;
    }

    /*------ implements ------*/

    /**
     * @param CategoryOption $categoryOption
     * @param StorageOption $storageOption
     * @param string $optionId
     * @return bool
     */
    public function has(
        CategoryOption $categoryOption,
        StorageOption $storageOption,
        string $optionId
    ) : bool
    {
        $option = $this->find($categoryOption, $storageOption, $optionId);
        return isset($option);
    }


    public function find(
        CategoryOption $categoryOption,
        StorageOption $storageOption,
        string $optionId
    ): ? Option
    {
        $storageOption = $this->parseOption($storageOption);
        return $this->isLoaded($categoryOption)
            ? $this->readFromOptionCache($categoryOption, $optionId)
            : $this->readFromFile($categoryOption, $storageOption, $optionId);
    }


    public function save(
        CategoryOption $categoryOption,
        StorageOption $storageOption,
        Option $option,
        bool $notExists = false
    ): bool
    {
        $storageOption = $this->parseOption($storageOption);
        // 保证读取.
        $this->boot($categoryOption, $storageOption);

        $cateId = $categoryOption->getId();
        $optionId = $option->getId();

        if ($notExists && isset($this->optionCaches[$cateId][$optionId])) {
            return false;
        }

        $this->optionCaches[$cateId][$optionId] = $option;
        // 存储到介质.
        if ($storageOption->isDir) {
            $this->saveToDir($categoryOption, $storageOption, $option);
        } else {
            $this->saveToFile($categoryOption, $storageOption);
        }

        return true;
    }

    public function delete(
        CategoryOption $categoryOption,
        StorageOption $storageOption,
        string $id,
        string ...$ids
    ): int
    {
        array_unshift($ids, $id);

        $cateId = $categoryOption->getId();
        $storage = $this->parseOption($storageOption);

        // 先清空 allId 配置
        unset($this->allIds[$cateId]);

        $unset = [];
        foreach ($ids as $id) {
            $option = $this->find($categoryOption, $storage, $id);
            if (isset($option)) {
                unset($this->optionCaches[$cateId][$id]);
                $unset[] = $id;
            }
        }

        if ($unset) {
            $this->deleteToFile($categoryOption, $storage, $unset);
        }

        return count($unset);
    }

    public function findByIds(
        CategoryOption $categoryOption,
        StorageOption $storageOption,
        array $ids
    ): array
    {
        $storageOption = $this->parseOption($storageOption);

        $result = [];
        foreach ($ids as $id) {
            $option = $this->find($categoryOption, $storageOption, $id);
            if (isset($option)) {
                $result[$option->getId()] = $option;
            }
        }
        return $result;
    }

    public function eachOption(
        CategoryOption $categoryOption,
        StorageOption $storageOption
    ): \Generator
    {
        $ids = $this->getAllIds($categoryOption, $storageOption);
        foreach ($ids as $id) {
            yield $this->find($categoryOption, $storageOption, $id);
        }
    }


    public function getAllIds(
        CategoryOption $categoryOption,
        StorageOption $storageOption
    ): array
    {
        $cateId = $categoryOption->getId();
        if (isset($this->allIds[$cateId])) {
            return $this->allIds[$cateId];
        }
        $storageOption = $this->parseOption($storageOption);
        $this->loadFile($categoryOption, $storageOption);

        $options = $this->optionCaches[$cateId] ?? [];

        return $this->allIds[$cateId] = array_keys($options);
    }

    public function count(
        CategoryOption $categoryOption,
        StorageOption $storageOption
    ): int
    {
        $storageOption = $this->parseOption($storageOption);
        $this->loadFile($categoryOption, $storageOption);
        $cateId = $categoryOption->getId();
        return count($this->optionCaches[$cateId]);
    }

    public function searchIds(
        CategoryOption $categoryOption,
        StorageOption $storageOption,
        string $wildCardId
    ): array
    {
        $ids = $this->getAllIds($categoryOption, $storageOption);
        return StringUtils::wildcardSearch($wildCardId, $ids);
    }

    public function eachId(
        CategoryOption $categoryOption,
        StorageOption $storageOption
    ): \Generator
    {
        $ids = $this->getAllIds($categoryOption, $storageOption);
        foreach ($ids as $id) {
            yield $id;
        }
    }

    public function paginateIds(
        CategoryOption $categoryOption,
        StorageOption $storageOption,
        int $offset = 0,
        int $limit = 20
    ): array
    {
        $ids = $this->getAllIds($categoryOption, $storageOption);
        if (empty($ids)) {
            return [];
        }

        return array_slice($ids, $offset, $limit);
    }

    public function flush(
        CategoryOption $categoryOption,
        StorageOption $storageOption
    ): bool
    {
        $ids = $this->getAllIds($categoryOption, $storageOption);
        $id = array_shift($ids);
        if (!isset($id)) {
            return true;
        }
        return (bool) $this->delete($categoryOption, $storageOption, $id, ...$ids);
    }


    public function __destruct()
    {
        $this->optionCaches = [];
        $this->resources = [];
        $this->optionPath = [];
        $this->allIds = [];
    }

}