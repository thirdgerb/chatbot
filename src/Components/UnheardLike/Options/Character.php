<?php


namespace Commune\Components\UnheardLike\Options;


use Commune\Support\Option;

/**
 * 角色的配置.
 *
 * @property-read string $id 角色的代号
 * @property-read string $name  角色的本名, 也是需要正确标注的内容.
 *
 * @property-read string $desc 角色声音类型的描述.
 *
 * @property-read Action[] $timeline 角色的时间线.
 */
class Character extends Option
{
    const IDENTITY = 'id';

    protected static $associations =[
        'timeline[]' => Action::class,
    ];

    public static function stub(): array
    {
        return [
            'id' => '',
            'name' => '',
            'desc' => '',
            'timeline' => [],
        ];
    }

    protected function init(array $data): array
    {
        $timeline = $data['timeline'] ?? [];
        if (!empty($timeline)) {
            $data['timeline'] = $this->wrapTimeline($timeline);
        }

        $data['id'] = trim($data['id']);

        return parent::init($data);
    }

    protected function wrapTimeline(array $actions) : array
    {
        $at = '???';
        $result = [];
        while ($action = array_shift($actions)) {

            // 对地点进行赋值.
            if (empty($action['at'])) {
                $action['at'] = $at;
            } else {
                $at = $action['at'];
            }

            $result[] = $action;
        }

        return $result;
    }

    public static function validate(array $data): ? string
    {
        $keys = array_keys(static::stub());
        foreach ($keys as $key) {
            if (empty($data[$key])) {
                return "$key is required";
            }
        }
        return null;
    }


}