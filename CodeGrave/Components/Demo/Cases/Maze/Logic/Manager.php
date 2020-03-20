<?php


namespace Commune\Components\Demo\Cases\Maze\Logic;


/**
 * 迷宫小游戏的逻辑.
 */
class Manager
{
    const SECTION_CELLS = [
        self::CELL_NORMAL,
        self::CELL_DARKNESS,
        self::CELL_ICE,
        self::CELL_GAS
    ];

    const SECTION_LOCATIONS = [
        [0, 1, 0, 2],
        [0, 2, 3, 4],
        [3, 4, 2, 4],
        [2, 4, 0, 1],
    ];

    const TRAP_TO_ITEM = [
        self::CELL_ICE => self::ITEM_SHOES,
        self::CELL_DARKNESS => self::ITEM_FIRE,
        self::CELL_GAS => self::ITEM_MASK
    ];

    const CELL_ITEMS = [
        self::ITEM_KEY,
        self::ITEM_SHOES,
        self::ITEM_FIRE,
        self::ITEM_MASK,
    ];


    const CELL_WALL = 'w';
    const CELL_NORMAL = 'n';
    const CELL_BORN = 'b';
    const CELL_DARKNESS = 'd';
    const CELL_ICE = 'i';
    const CELL_GAS = 'g';


    const ITEM_KEY = 'k';
    const ITEM_SHOES = 's';
    const ITEM_FIRE = 'f';
    const ITEM_MASK = 'm';

    const MAX_X = 4;
    const MAX_Y = 4;
    const BORN_LOCATION = [2, 2];


    const DIRECTIONS  = [
        self::DIRECTION_NORTH,
        self::DIRECTION_EAST,
        self::DIRECTION_SOUTH,
        self::DIRECTION_WEST,
    ];

    const DIRECTION_NORTH = 0;
    const DIRECTION_EAST = 1;
    const DIRECTION_SOUTH = 2;
    const DIRECTION_WEST = 3;

    const TOWARD_FRONT = 0;
    const TOWARD_LEFT = -1;
    const TOWARD_RIGHT = 1;
    const TOWARD_BACK = 2;

    const POINTS_TOTAL = 200;

    public static function makePoints(int $step) : int
    {
        $points = self::POINTS_TOTAL - $step;
        return $points > 0 ? $points : 0;
    }

    /**
     * 生成一个5x5 的二维数组作为地图.
     *
     * @return array
     */
    public static function initializeMap() : array
    {
        return Map::initializeMap();
    }


    /**
     * @param array $map
     * @param int $y
     * @param int $x
     * @param array $items
     * @return array list of string $cell, bool $success, string $getItem, bool $win
     */
    public static function goLocation(array $map, int $y, int $x,  array $items) : array
    {
        if ($y < 0 || $x < 0) {
            return [self::CELL_WALL, false, null, false];
        }

        $wall = ! isset($map[$y][$x]);

        if ($wall) {
            return [self::CELL_WALL, false, null, false];
        }

        $cellInfo = $map[$y][$x];
        $cell = $cellInfo[0];
        $item = $cellInfo[1] ?? null;

        if (
            // 是陷阱
            array_key_exists($cell, Manager::TRAP_TO_ITEM)
            // 道具不存在
            && !in_array(Manager::TRAP_TO_ITEM[$cell], $items)
        ) {
            return [$cell, false, null, false];
        }

        if ($cell === self::CELL_BORN && in_array(self::ITEM_KEY, $items)) {
            return [$cell, true, null, true];
        }

        // 遇到了一个没有的道具
        if (isset($item) && !in_array($item, $items)) {
           return [$cell, true, $item, false];
        }

        return [$cell, true, null, false];
    }

    public static function parseTowardToDirection(int $lastDirection, int $currentToward) : int
    {
        $sum = (4 + $lastDirection + $currentToward) % 4;
        return self::DIRECTIONS[$sum];
    }

    public static function parseLocationOfDirection(int $y, int $x, int $direction) : array
    {
        switch ($direction) {
            case self::DIRECTION_NORTH:
                return [$y - 1, $x];
            case self::DIRECTION_EAST:
                return [$y, $x + 1];
            case self::DIRECTION_SOUTH:
                return [$y + 1, $x];
            case self::DIRECTION_WEST:
            default:
                return [$y, $x - 1];
        }
    }

}