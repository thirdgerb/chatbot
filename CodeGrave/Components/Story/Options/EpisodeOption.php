<?php


namespace Commune\Components\Story\Options;


use Commune\Components\Story\Tasks\EpisodeTask;
use Commune\Support\Option;

/**
 * 每一章的定义.
 *
 * @property-read string $id 章节的唯一ID
 * @property-read string $option 章节的选项.
 * @property-read string $title 章节的标题
 * @property-read array $defaultSlots 默认的参数.
 * @property-read string[] $middleware 每一 stage 都要执行的 middleware
 * @property-read StageOption[] $stages 本章可用的小节.
 * @property-read string $class 实例的类名.
 *
 */
class EpisodeOption extends Option
{
    const IDENTITY = 'id';

    protected static $associations = [
        'stages[]' => StageOption::class,
    ];

    public static function stub(): array
    {
        return [
            'id' => '',
            'title' => '',
            'option' => '',
            'middleware' => [],
            'defaultSlots' => [],
            'stages' => [],
            'class' => EpisodeTask::class,
        ];
    }

    public static function validate(array $data): ? string
    {
        $id = $data['id'] ?? '';
        $option = $data['option'] ?? '';
        $title = $data['title'] ?? '';

        if (empty($id)) {
            return 'id is empty';
        }

        if (empty($option)) {
            return 'option is empty';
        }

        if (empty($title)) {
            return 'title is empty';
        }

        return null;
    }

    public function getStageOption(string $stageName) : ? StageOption
    {
        foreach ($this->stages as $stage) {
            if ($stage->id === $stageName) {
                return $stage;
            }
        }
        return null;
    }

}