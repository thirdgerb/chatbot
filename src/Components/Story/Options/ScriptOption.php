<?php

namespace Commune\Components\Story\Options;


use Commune\Components\Story\Tasks\ScriptMenu;
use Commune\Support\Option;

/**
 * @property-read string $id 脚本的唯一ID
 * @property-read string $version 脚本的版本. 版本更新后可以重置记忆.
 * @property-read string $title 脚本的名称.
 * @property-read string[] $defaultEpisodes 默认可选的章节
 * @property-read string $messagePrefix replyID的公共前缀.
 * @property-read array $defaultSlots 整个脚本默认的 slots, 可用于 reply
 * @property-read ItemOption[]  $itemDef 道具的定义.
 * @property-read EpisodeOption[] $episodes 章节的定义.
 * @property-read string $class 脚本实例的类名.
 * @property-read CommandsOption $commands
 */
class ScriptOption extends Option
{
    const IDENTITY = 'id';

    protected static $associations = [
        'itemDef[]' => ItemOption::class,
        'episodes[]' => EpisodeOption::class,
        'commands' => CommandsOption::class,
    ];

    public static function stub(): array
    {
        return [
            'id' => '',
            'title' => '',
            'messagePrefix' => '',
            'defaultSlots' => [
            ],
            'defaultEpisodes' => [
            ],
            'itemDef' => [
                //ItemOption::stub(),
            ],
            'episodes' => [
                //EpisodeOption::stub(),
            ],
            'class' => ScriptMenu::class,
            'commands' => CommandsOption::stub(),
        ];
    }

    public function getBrief() : string
    {
        return $this->title;
    }

    public function getEpisodeOption(string $episodeName) : ? EpisodeOption
    {
        foreach ($this->episodes as $episode) {
            if ($episode->id === $episodeName) {
                return $episode;
            }
        }

        return null;
    }

    public function getEpisodeIdToTitles() : array
    {
        $episodeNames = [];
        foreach ($this->episodes as $episode) {
            $episodeNames[$episode->id] = $episode->title;
        }

        return $episodeNames;
    }

    public function getEpisodeTitleToIds() : array
    {
        $episodeIds = [];
        foreach ($this->episodes as $episode) {
            $episodeIds[$episode->title] = $episode->id;
        }
        return $episodeIds;
    }

    public function getFirstEpisode() : ? EpisodeOption
    {
        foreach ($this->episodes as $episode) {
            return $episode;
        }
        return null;
    }

    public function getLastEpisode() : ? EpisodeOption
    {
        $result = null;
        foreach ($this->episodes as $episode) {
            $result = $episode;
        }
        return $result;
    }

    /**
     * @param string[] $stageNames
     * @return StageOption[]
     */
    public function getStages(array $stageNames) : array
    {
        $result = [];
        foreach ($this->episodes as $episode) {
            foreach ($episode->stages as $stage) {

                if (in_array($stage->id, $stageNames)) {
                    $result[] = $stage;
                }

            }
        }
        return $result;
    }

    public function getEndings(array $endings) : array
    {
        $result = [];
        foreach ($this->episodes as $episode) {
            foreach ($episode->stages as $stage) {
                $ending = static::makeEndingName($episode, $stage);
                if (in_array($ending, $endings)) {
                    $result[] = $stage;
                }
            }
        }
        return $result;
    }

    public static function makeEndingName(EpisodeOption $episode, StageOption $stage) : string
    {
        return $episode->id .'::' . $stage->id;
    }

    public function getStageOption(?string $episodeId, ?string $stageId) : ? StageOption
    {
        if (empty($episodeId) || empty($stageId)) {
            return null;
        }
        foreach ($this->episodes as $episode) {
            if ($episode->id !== $episodeId) {
                continue;
            }
            foreach ($episode->stages as $stage) {
                if ($stage->id === $stageId) {
                    return $stage;
                }
            }
        }
        return null;
    }


    public function parseReplyId(string $replyId) : string
    {
        return $this->messagePrefix . '.' . $replyId;
    }

    public function parseEpisodeId(string $id) : string
    {
        return static::makeEpisodeName($this->id, $id);
    }

    public static function makeEpisodeName(string $scriptId, string $episodeId) : string
    {
        return "$scriptId.$episodeId";
    }
}