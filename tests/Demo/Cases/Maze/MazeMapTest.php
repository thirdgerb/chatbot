<?php


namespace Commune\Test\Demo\Cases\Maze;


use Commune\Components\Demo\Cases\Maze\Logic\Manager;
use Commune\Components\Demo\Cases\Maze\Logic\Map;
use PHPUnit\Framework\TestCase;

class MazeMapTest extends TestCase
{

    public function testInitialize()
    {
        $map = Map::initialize();

        $this->assertEquals(5, count($map));
        foreach ($map as $line) {
            $this->assertEquals(5, count($line));
        }

        $born = Manager::BORN_LOCATION;
        $this->assertEquals(Manager::CELL_BORN, $map[$born[0]][$born[1]]);

        $n = Manager::CELL_NORMAL;
        $b = Manager::CELL_BORN;
        $this->assertEquals(<<<EOF
$n $n $n $n $n
$n $n $n $n $n
$n $n $b $n $n
$n $n $n $n $n
$n $n $n $n $n
EOF
    ,  $output = Map::outputMap($map)
);
    }

    public function testRandomSections()
    {
        $sections = Map::randomSections();

        $types = Manager::SECTION_CELLS;

        foreach ($types as $type) {
            $this->assertTrue(in_array($type, $sections));
        }
    }

    public function testSetSections()
    {
        $map = Map::initialize();

        $map = Map::setSections($map, [1,2,3,4]);

        $this->assertEquals(<<<EOF
1 1 1 2 2
1 1 1 2 2
4 4 b 2 2
4 4 3 3 3
4 4 3 3 3
EOF
        ,
        $output = Map::outputMap($map)
        );
    }

    public function testFetchItemSections()
    {
        $sections = Map::randomSections();

        // 生成 item 对应的区域
        $itemSections = Map::fetchItemSections($sections);

        foreach (Manager::CELL_ITEMS as $item) {

            // 所有道具应该都有对应区域.
            $this->assertArrayHasKey($item, $itemSections);
            $trap = $sections[$itemSections[$item]];

            // 普通房间不能出现 key
            if ($trap === Manager::CELL_NORMAL) {
                $this->assertNotEquals(Manager::ITEM_KEY, $item);

            } else {
                $this->assertNotEquals(Manager::TRAP_TO_ITEM[$trap], $item);
            }
        }
    }

    public function testFinalMap()
    {
        $map = Map::initializeMap();

        $itemNum = [];
        $cellNum = [];
        $itemCell = [];
        $this->assertEquals(5, count($map));
        foreach ($map as $y => $line) {
            $this->assertEquals(5, count($line));
            foreach ($line as $x => $cellStr) {
                $cell = $cellStr[0];
                $item = $cellStr[1] ?? null;
                $cellNum[$cell] = isset($cellNum[$cell]) ? $cellNum[$cell] + 1 : 1;
                if (isset($item)) {
                    $itemCell[$item][] = $cell;
                    $itemNum[$item] = isset($itemNum[$item]) ? $itemNum[$item] + 1 : 1;
                }
            }
        }


        $this->assertEquals(1, $cellNum[Manager::CELL_BORN]);
        unset($cellNum[Manager::CELL_BORN]);

        $before = 0;
        foreach ($cellNum as $cell => $num) {
            if ($before) {
                $this->assertEquals($before, $num);
            }
            $before = $num;
        }

        $itemToCell = array_flip(Manager::TRAP_TO_ITEM);

        foreach ($itemCell as $item => $cells) {
            $this->assertEquals(1, count($cells));

            if ($item === Manager::ITEM_KEY) {
                $this->assertNotEquals(Manager::CELL_BORN, $cells[0]);
            } else {
                $this->assertNotEquals($cells[0], $itemToCell[$item]);
            }
        }

        //var_dump(Map::outputMap($map));
    }
}