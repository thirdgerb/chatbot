<?php


namespace Commune\Support\OptionRepo\Storage;


use Commune\Support\Option;
use Commune\Support\OptionRepo\Options\StorageMeta;
use Commune\Support\OptionRepo\Contracts\RootOptionStage;
use Symfony\Component\Finder\Finder;

abstract class RootFileStorage implements RootOptionStage
{

    /**
     * @var Option[][]
     */
    protected $options = [];

    protected $ext = '';

    abstract protected function parseOptionToString(Option $option) : string;

    abstract protected function parseStringToOption(string $optionName, string $content) : ? Option;

    /**
     * @param Option $option
     * @param FileStorageMeta $meta
     */
    public function save(
        Option $option,
        StorageMeta $meta
    ): void
    {
        $meta->caching && $this->setLoaded(get_class($option), $option->getId(), $option);
        $content = $this->parseOptionToString($option);
        $file = $this->getFilePath($meta, $option->getId());
        file_put_contents($file, $content);
    }

    protected function hasLoaded(string $optionName, string $id) : bool
    {
        return isset($this->options[$optionName][$id]);
    }

    protected function getLoaded(string $optionName, string $id) : ? Option
    {
        $loaded = $this->options[$optionName][$id];
        return $loaded !== false ? $loaded : null;
    }

    protected function setLoaded(string $optionName, string $id, Option $option = null) : void
    {
        $this->options[$optionName][$id] = $option ?? false;
    }

    protected function unsetLoaded(string $optionName, string $id) : void
    {
        unset($this->options[$optionName][$id]);
    }



    /**
     * @param string $optionName
     * @param string $id fileBaseName
     * @param FileStorageMeta $meta
     * @return Option|null
     */
    public function get(
        string $optionName,
        string $id,
        StorageMeta $meta
    ): ? Option
    {
        if ($meta->caching && $this->hasLoaded($optionName, $id)) {
            return $this->getLoaded($optionName, $id);
        }

        $path = $this->getFilePath($meta, $id);

        if (!file_exists($path)) {
            $meta->caching and $this->setLoaded($optionName, $id, null);
        }

        $result = $this->readOption($path, $optionName);

        if (empty($result)) {
            $meta->caching and $this->setLoaded($optionName, $id, null);
            return null;
        }

        $option = new $optionName($result);
        $meta->caching and $this->setLoaded($optionName, $id, $option);

        return $option;
    }

    protected function readOption(string $path, string $optionName) : ? Option
    {
        $data = file_get_contents($path);
        return $this->parseStringToOption($optionName, $data);
    }

    protected function getFilePath(FileStorageMeta $meta, string $id) : string
    {
        $dir = $meta->directory;
        $filename = $id;
        return rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename . '.' . $this->ext;
    }

    /**
     * @param string $optionName
     * @param string $id
     * @param FileStorageMeta $meta
     * @return bool
     */
    public function has(
        string $optionName,
        string $id,
        StorageMeta $meta
    ): bool
    {
        return $this->hasLoaded($optionName, $id) || file_exists($this->getFilePath($meta, $id));
    }


    /**
     * @param string $optionName
     * @param FileStorageMeta $meta
     * @param string ...$ids
     */
    public function delete(string $optionName, StorageMeta $meta, string ...$ids): void
    {
        foreach ($ids as $id) {
            $this->unsetLoaded($optionName, $id);
            $path = $this->getFilePath($meta, $id);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }

    public function lockId(string $optionName, string $id, StorageMeta $meta): bool
    {
        // 文件作为 root 层, 不锁了, 太麻烦.
        return true;
    }

    protected function prepareFinder(FileStorageMeta $meta, string $pattern = null) : Finder
    {
        $pattern = $pattern ?? '/\.'.$this->ext.'$/';
        return (new Finder())
            ->in($meta->directory)
            ->depth(0)
            ->name($pattern);

    }

    /**
     * @param string $optionName
     * @param FileStorageMeta $meta
     * @return int
     */
    public function count(string $optionName, StorageMeta $meta): int
    {
        return $this->prepareFinder($meta)->count();
    }

    public function paginateIdToBrief(string $optionName, StorageMeta $meta, int $page = 1, int $lines = 20): array
    {
        $page --;
        $page = $page > 0 ? $page : 0;

        $start = $page * $lines;
        $end = ($page + 1 ) * $lines;

        $i = 0;

        $result = [];
        foreach ($this->eachOption($optionName, $meta) as $option) {
            /**
             * @var Option $option
             */
            if ($i >= $start && $i < $end) {
                $result[$option->getId()]  = $option->getBrief();
            }
            $i ++;
        }

        return $result;
    }

    /**
     * @param string $optionName
     * @param FileStorageMeta $meta
     * @return string[]
     */
    public function getAllOptionIds(string $optionName, StorageMeta $meta): array
    {
        $ids = [];
        foreach ($this->prepareFinder($meta) as $file) {
            $ids[] = $this->getIdOfFile($file);
        }

        return $ids;
    }

    protected function getIdOfFile(\SplFileInfo $file) : string
    {
        return str_replace('.'.$file->getExtension(), '', $file->getBasename());
    }

    /**
     * @param string $optionName
     * @param array $ids
     * @param FileStorageMeta $meta
     * @return array
     */
    public function findOptionsByIds(string $optionName, array $ids, StorageMeta $meta): array
    {
        $pattern  = '/\(' . implode('|', $ids) . '\)\.'. $this->ext .'$/';
        $options = [];
        foreach ($this->prepareFinder($meta, $pattern) as $file) {
            $id = $this->getIdOfFile($file);
            $option = $this->get($optionName, $id, $meta);
            $options[$id] = $option;
        }

        return $options;
    }

    /**
     * @param string $optionName
     * @param FileStorageMeta $meta
     * @return \Generator
     */
    public function eachOption(string $optionName, StorageMeta $meta): \Generator
    {
        foreach ($this->prepareFinder($meta) as $file) {
            $id = $this->getIdOfFile($file);
            $option = $this->get($optionName, $id, $meta);
            if (isset($option)) {
                yield $option;
            }
        }
    }


    /**
     * @param string $optionName
     * @param string $query
     * @param FileStorageMeta $meta
     * @return array
     */
    public function searchOptionsByQuery(string $optionName, string $query, StorageMeta $meta): array
    {
        $result = [];
        foreach ($this->eachOption($optionName, $meta) as $option) {
            /**
             * @var Option $option
             */
            if (strstr($option->getBrief(), $query)) {
                $result[] = $option;
            }
        }
        return $result;
    }



}