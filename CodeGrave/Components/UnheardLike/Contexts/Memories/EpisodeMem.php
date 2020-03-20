<?php


namespace Commune\Components\UnheardLike\Contexts\Memories;


use Commune\Chatbot\App\Memories\MemoryDef;

/**
 * @property string|null $t 当前时间
 * @property string|null $follow 当前跟随对象
 * @property string|null $at 当前所处房间.
 *
 *
 * @property string $episodeName 当前章节
 * @property string $userId 用户的ID
 * @property string[] $marked 玩家当前标记的角色名. id => nickname   例如 甲=>张三
 * @property bool  $win  是否胜利了.
 */
class EpisodeMem extends MemoryDef
{
    const DESCRIPTION = '用户玩到某一章的进度记忆';

    protected $user;

    protected $episode;

    public function __construct(string $episodeName, string $userId)
    {
        $this->user = $userId;
        $this->episode = $episodeName;
        $this->_contextId = md5(static::class .';episode:' . $episodeName .';user:'. $userId);
        parent::__construct();
    }

    protected function init(): array
    {
        return [
            'episodeName' => $this->episode,
            'userId' => $this->user,
            'marked' => [],
            'win' => false,
            't' => null,
            'follow' => null,
            'at' => null,
        ];
    }


    /**
     * @return string
     */
    public function getMarked() : string
    {
        $result = '';

        foreach ($this->marked as $id => $name) {
            $result .= " \n$id : $name";
        }

        return $result;
    }


}