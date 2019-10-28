<?php


namespace Commune\Components\UnheardLike\Options;


use Commune\Support\Option;

/**
 * @property-read string $time 当前帧的时间
 * @property-read string[] $roleRooms 所有角色所在的位置  string role => string location
 * @property-read string[][] $lines 每个位置要播放的内容. string location => [ string ... $lines ],
 * @property-read string|null $next  下一帧的位置. 为 null 表示到头了.
 */
class Frame extends Option
{
    const IDENTITY = 'time';

    public static function stub(): array
    {
        return [
            'time' => '',
            'roleRooms' => [],
            'lines' => [],
            'next' => null,
        ];
    }

    public function setNextFrame(string $time) : void
    {
        $this->data['next'] = $time;
    }

    public function addLineToRoom(string $location, string $line) : void
    {
        $this->data['lines'][$location][] = $line;
    }

    public function getRoomLines(string $location) : array
    {
        return $this->data['lines'][$location] ?? [];
    }

    public function hasRoleRoom(string $role) : bool
    {
        return isset($this->data['roleRooms'][$role]);
    }

    public function setRoleRoom(string $characterId, string $location) : void
    {
        $this->data['roleRooms'][$characterId] = $location;
    }

    public function getRoleRoom(string $role) : string
    {
        return isset($this->data['roleRooms'][$role])
            ? $this->data['roleRooms'][$role]
            : '???';
    }


}