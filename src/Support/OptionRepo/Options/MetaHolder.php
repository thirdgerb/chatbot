<?php


namespace Commune\Support\OptionRepo\Options;


use Commune\Support\Option;
use Commune\Support\OptionRepo\Storage\FileStorageMeta;
use Commune\Support\OptionRepo\Storage\Json\YamlStorageMeta;

/**
 * StorageMeta 的 placeHolder. 会用 data 的值生成 meta 定义的 storageMeta
 *
 * @property-read string $meta StorageMeta 的类名
 * @property-read array $config StorageMeta 的值
 */
class MetaHolder extends Option
{
    public static function stub(): array
    {
        return [
            'meta' => '',
            'config' => [],
        ];
    }

    public static function validate(array $data): ? string
    {
        if (empty($data['meta'])) {
            return 'meta field is invalid';
        }

        if (!is_a($data['meta'], StorageMeta::class, TRUE)) {
            return 'meta field must be class name of sub class of '. StorageMeta::class;
        }

        return parent::validate($data);
    }

    protected $metaObj;

    /**
     * @return StorageMeta
     */
    public function getStorageMeta() : StorageMeta
    {
        if (isset($this->metaObj)) return $this->metaObj;
        $className = $this->meta;
        return $this->metaObj = new $className ($this->config);
    }



}