<?php


namespace Commune\Demo\App\Cases\Maze\Logic;


class Map
{


    public static function initializeMap() : array
    {
        $map = self::initialize();
        $sections = self::randomSections();

        $map = self::setSections($map, $sections);
        $itemsSections = self::fetchItemSections($sections);

        return self::setItemToMap($map, $itemsSections);
    }

    public static function setItemToMap(array $map, array $itemsSections) : array
    {
        $sectionLocations = Manager::SECTION_LOCATIONS;
        foreach ($itemsSections as $item => $section) {
            list($yMin, $yMax, $xMin, $xMax) = $sectionLocations[$section];

            $y = rand($yMin, $yMax);
            $x = rand($xMin, $xMax);

            $map[$y][$x] .= $item;
        }

        return $map;
    }

    public static function fetchItemSections(array $sections) : array
    {
        $result  = [];
        $trapToSections = array_flip($sections);

        $traps = array_keys(Manager::TRAP_TO_ITEM);
        $trapItems = array_values(Manager::TRAP_TO_ITEM);

        // 随机选了一个区域做放钥匙的地方.
        $keySection = self::arrRandom($traps);
        $result[Manager::ITEM_KEY] = $trapToSections[$keySection];
        unset($trapToSections[$keySection]);

        // 不参与排序, 只选剩下的.
        $normalSection = $trapToSections[Manager::CELL_NORMAL];
        unset($trapToSections[Manager::CELL_NORMAL]);

        foreach ($trapToSections as $trap => $section) {
            $item = array_shift($trapItems);
            if (
                isset(Manager::TRAP_TO_ITEM[$trap])
                && $item === Manager::TRAP_TO_ITEM[$trap]
            ) {
                $trapItems[] = $item;
                // 不行就换下位置.
                $item = array_shift($trapItems);
            }
            $result[$item] = $section;
        }

        $result[array_shift($trapItems)] = $normalSection;
        return $result;
    }

    public static function randLocation(array $location) : array
    {
        list($yMin, $yMax, $xMin, $xMax) = $location;
        $y = rand($yMin, $yMax);
        $x = rand($xMin, $xMax);
        return [$y, $x];
    }

    public static function arrRandom(array $items)
    {
        $i = rand(1, count($items)) - 1;
        return array_values($items)[$i];
    }

    public static function setSections(array $map, array $sectionTypes)
    {
        $sections = Manager::SECTION_LOCATIONS;

        foreach ($sections as $index => list($yMin, $yMax, $xMin, $xMax)) {

            for($y = $yMin; $y <= $yMax; $y ++) {
                for($x = $xMin; $x <= $xMax; $x++) {
                    $map[$y][$x] = $sectionTypes[$index];
                }
            }
        }

        return $map;
    }

    /**
     * @return array
     */
    public static function initialize() : array
    {
        $map = [];
        for ($i = 0; $i <= Manager::MAX_Y; $i ++) {
            for ($j = 0; $j <= Manager::MAX_X; $j ++) {
                $map[$i][$j] = Manager::CELL_NORMAL;
            }
        }

        $born = Manager::BORN_LOCATION;
        $map[$born[0]][$born[1]] = Manager::CELL_BORN;
        return $map;
    }

    public static function outputMap(array $map) : string
    {
        return implode("\n", array_map(function(array $line){
            return implode(' ', $line);
        }, $map));
    }

    public static function randomSections() : array
    {
        $sections = Manager::SECTION_CELLS;

        $result = [];
        for ($i = 4; $i > 0; $i --) {
            $index = rand(1, $i) - 1;
            $result[] = $sections[$index];
            unset($sections[$index]);
            $sections = array_values($sections);
        }

        return $result;
    }

}