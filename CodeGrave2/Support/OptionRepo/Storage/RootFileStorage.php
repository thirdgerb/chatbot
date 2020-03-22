<?php


namespace Commune\Support\OptionRepo\Storage;


use Commune\Support\Struct;
use Commune\Support\OptionRepo\Options\CategoryMeta;
use Commune\Support\OptionRepo\Options\StorageMeta;
use Commune\Support\OptionRepo\Contracts\RootOptionStage;
use Symfony\Component\Finder\Finder;

/**
 * 从文件读取数据的存储介质. 通常用于根介质.
 * 用文件修改, 方便在仓库里携带, 同时也比后台的 web 界面更加方便.
 *
 * 文件仓库有两种:
 *
 * - 一种是, 直接把目录做成仓库, 每个文件是一个独立的option.
 * - 另一种是, 把一个文件当成仓库, 里面是一个大数组, 每一个元素是一个独立的option.
 *
 */
abstract class RootFileStorage implements RootOptionStage
{
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
    protected $optionFromFile = [];

    public function flush(
        CategoryMeta $category,
        StorageMeta $storage
    ): void
    {
        $options = $this->optionCaches[$category->getId()] ?? [];

        if (empty($options)) {
            return;
        }

        $this->delete($category, $storage, array_map(function(Option $o){
            return $o->getId();
        }, $options));
    }


    abstract protected function parseArrayToString(array $option, FileStorageMeta $meta): string;

    abstract protected function parseStringToArray(string $content): array;

    /**
     * @param CategoryMeta $category
     * @param FileStorageMeta $storage
     * @param Option[] $options
     */
    public function save(
        CategoryMeta $category,
        StorageMeta $storage,
        Option ...$options
    ): void
    {
        foreach ($options as $option) {
            $this->optionCaches[$category->getId()][$option->getId()] = $option;
        }

        if ($storage->isDir) {
            foreach ($options as $option) {
                $this->saveToDir($category, $storage, $option);
            }
        } else {
            $this->saveToFile($category, $storage);
        }
    }

    /**
     * 将整个目录存到一个文件.
     * @param CategoryMeta $category
     * @param FileStorageMeta $meta
     */
    protected function saveToFile(CategoryMeta $category, FileStorageMeta $meta) : void
    {
        $data = $this->getCategoryData($category);
        $data = array_values($data);
        $content = $this->parseArrayToString($data, $meta);
        file_put_contents($meta->path, $content);
    }


    protected function getCategoryData(CategoryMeta $category) : array
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
     * @param CategoryMeta $category
     * @param FileStorageMeta $meta
     * @param Option $option
     */
    protected function saveToDir(CategoryMeta $category, FileStorageMeta $meta, Option $option) : void
    {
        $cateId = $category->getId();
        $optionId = $option->getId();

        $path = $this->optionFromFile[$cateId][$optionId] ?? null;
        $path = $path ?? $this->getFileName($meta, $optionId);

        $content = $this->parseArrayToString($option->toArray(), $meta);

        file_put_contents($path, $content);
        $this->optionFromFile[$cateId][$optionId] = $path;
    }

    protected function getFileName(FileStorageMeta $meta, string $id = null) : string
    {
        $id = isset($id) ? DIRECTORY_SEPARATOR . $id : '';
        return rtrim($meta->path, DIRECTORY_SEPARATOR) . $id . '.' . $this->ext;
    }

    protected function readFromOptionCache(CategoryMeta $category, string $id) : ? Option
    {
        return $this->optionCaches[$category->getId()][$id] ?? null;
    }

    protected function loadFile(CategoryMeta $category, FileStorageMeta $meta) : void
    {
        if ($meta->isDir) {
            $this->loadDirAllFiles($category, $meta);
            return;
        }
        $cateId = $category->getId();
        if (isset($this->resources[$cateId])) {
            return;
        }

        $path = $meta->path;
        $data = $this->readFileArr($path);
        $this->resources[$cateId] = $meta->getId();
        if (empty($data)) {
            return;
        }

        foreach ($data as $optionArr) {
            $option = $meta->newOption($category->optionClazz, $optionArr, $path);
            $optionId = $option->getId();
            $this->optionCaches[$cateId][$optionId] = $option;
            $this->optionFromFile[$cateId][$optionId] = $path;
        }
    }

    protected function readFileArr(string $path) : ? array
    {
        $content = file_get_contents($path);
        if (empty($content)) {
            return null;
        }

        return $this->parseStringToArray($content);
    }

    protected function loadDirAllFiles(CategoryMeta $category, FileStorageMeta $meta) : void
    {
        $cateId = $category->getId();
        if (isset($this->resources[$cateId])) {
            return;
        }

        $this->resources[$cateId] = $meta->getId();

        $finder = new Finder();
        $finder = $finder
            ->in($meta->path)
            ->depth($meta->depth)
            ->name('/\.' . $this->ext . '$/');


        foreach ($finder as $file) {
            /**
             * @var \SplFileInfo $file
             */
            $filePath = $file->getRealPath();
            $optionArr = $this->readFileArr($filePath);
            if (isset($optionArr)) {
                $option = $meta->newOption($category->optionClazz, $optionArr, $filePath);
                $optionId = $option->getId();
                $this->optionCaches[$cateId][$optionId] = $option;
                $this->optionFromFile[$cateId][$optionId] = $filePath;
            }
        }
    }

    protected function readFromFile(CategoryMeta $category, FileStorageMeta $meta, string $id) : ? Option
    {
        $this->loadFile($category, $meta);
        return $this->readFromOptionCache($category, $id);

    }

    /**
     * @param CategoryMeta $category
     * @param FileStorageMeta $storage
     * @param string $id
     * @return Option|null
     */
    public function get(
        CategoryMeta $category,
        StorageMeta $storage,
        string $id
    ): ? Option
    {
        return $this->readFromOptionCache($category, $id) ?? $this->readFromFile($category, $storage, $id);
    }

    public function has(
        CategoryMeta $category,
        StorageMeta $storage,
        string $id
    ): bool
    {
        $option = $this->get($category, $storage, $id);
        return isset($option);
    }

    /**
     * @param CategoryMeta $category
     * @param FileStorageMeta $storage
     * @param string ...$ids
     */
    public function delete(
        CategoryMeta $category,
        StorageMeta $storage,
        string ...$ids
    ): void
    {
        $cateId = $category->getId();
        $unset = [];
        foreach ($ids as $id) {
            $option = $this->get($category, $storage, $id);
            if (isset($option)) {
                unset($this->optionCaches[$cateId][$id]);
                $unset[] = $id;
            }
        }

        if ($unset) {
            $this->deleteToFile($category, $storage, $unset);
        }
    }


    protected function deleteToFile(
        CategoryMeta $category,
        FileStorageMeta $storage,
        array $deletes
    ) : void
    {
        if (!$storage->isDir) {
            $this->saveToFile($category, $storage);
            return;
        }

        $cateId = $category->getId();
        foreach ($deletes as $id) {
            $fileName = $this->optionFromFile[$cateId][$id] ?? $this->getFileName($storage, $id);
            unset($this->optionFromFile[$cateId][$id]);
            @unlink($fileName);
        }
    }


    public function lockId(
        CategoryMeta $category,
        StorageMeta $storage,
        string $id
    ): bool
    {
        // 这一层通常做 root, 所以不锁了.
        return true;
    }

    /**
     * @param CategoryMeta $category
     * @param FileStorageMeta $storage
     * @return int
     */
    public function count(CategoryMeta $category, StorageMeta $storage): int
    {
        $this->loadFile($category, $storage);
        return count($this->optionCaches[$category->getId()] ?? []);
    }

    public function paginateIdToBrief(CategoryMeta $category, StorageMeta $storage, int $page = 1, int $lines = 20): array
    {
        $page -- ;
        $page = $page > 0 ? $page : 0;
        $start = $lines * $page;
        $end = $lines * ($page + 1);

        $i = 0;
        $result = [];
        foreach ($this->eachOption($category, $storage) as $option) {

            if ($i >= $end) {
                break;
            }

            if ($i >= $start) {
                $result[$option->getId()] = $option;
            }
            $i ++;
        }
        return $result;
    }

    /**
     * @param CategoryMeta $category
     * @param FileStorageMeta $storage
     * @return array
     */
    public function getAllOptionIds(CategoryMeta $category, StorageMeta $storage): array
    {
        $this->loadFile($category, $storage);
        return array_map(function(Option $option) {
            return $option->getId();
        }, $this->optionCaches[$category->getId()] ?? []);
    }

    /**
     * @param CategoryMeta $category
     * @param FileStorageMeta $storage
     * @param array $ids
     * @return Option[]
     */
    public function findOptionsByIds(CategoryMeta $category, StorageMeta $storage, array $ids): array
    {
        $this->loadFile($category, $storage);
        $result = [];
        foreach ($ids as $id) {
            $option = $this->get($category, $storage, $id);
            if (isset($option)) $result[$option->getId()] = $option;
        }
        return $result;
    }

    public function searchOptionsByQuery(CategoryMeta $category, StorageMeta $storage, string $query): array
    {
        $result = [];
        foreach ($this->eachOption($category, $storage) as $option) {

            if (strstr($option->getBrief(), $query)) {
                $result[$option->getId()] = $option;
            }
        }
        return $result;
    }

    /**
     * @param CategoryMeta $category
     * @param FileStorageMeta $storage
     * @return Option[]
     */
    public function eachOption(CategoryMeta $category, StorageMeta $storage): \Generator
    {
        $this->loadFile($category, $storage);
        $options = $this->optionCaches[$category->getId()] ?? [];
        foreach ($options as $option) {
            yield $option;
        }
    }

}