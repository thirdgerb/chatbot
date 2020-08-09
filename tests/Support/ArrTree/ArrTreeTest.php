<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Support\ArrTree;

use Commune\Support\ArrTree\Tree;
use PHPUnit\Framework\TestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ArrTreeTest extends TestCase
{
    public function testTreeBuild()
    {
        $rootName = 'root';
        $data = [
            'a' => [
                'b' => [
                    'c'
                ],
                'd',
            ],
            'e'
        ];

        $tree = new Tree();
        $tree->build($data, $rootName);

        $b = $tree->branches['b'];
        $this->assertEquals('a', $b->parent->name);
        $this->assertEquals('d', $b->younger->name);
        $this->assertEquals(1, count($b->children));



        $this->assertEquals(
            array (
                'root' =>
                    array (
                        'name' => 'root',
                        'parent' => NULL,
                        'elder' => NULL,
                        'younger' => NULL,
                        'children' =>
                            array (
                                'a',
                                'e',
                            ),
                        'depth' => 0,
                    ),
                'a' =>
                    array (
                        'name' => 'a',
                        'parent' => 'root',
                        'elder' => NULL,
                        'younger' => 'e',
                        'children' =>
                            array (
                                'b',
                                'd',
                            ),
                        'depth' => 1,
                    ),
                'b' =>
                    array (
                        'name' => 'b',
                        'parent' => 'a',
                        'elder' => NULL,
                        'younger' => 'd',
                        'children' =>
                            array (
                                'c',
                            ),
                        'depth' => 2,
                    ),
                'c' =>
                    array (
                        'name' => 'c',
                        'parent' => 'b',
                        'elder' => NULL,
                        'younger' => NULL,
                        'children' =>
                            array (
                            ),
                        'depth' => 3,
                    ),
                'd' =>
                    array (
                        'name' => 'd',
                        'parent' => 'a',
                        'elder' => 'b',
                        'younger' => NULL,
                        'children' =>
                            array (
                            ),
                        'depth' => 2,
                    ),
                'e' => [
                    'name' => 'e',
                    'parent' => 'root',
                    'elder' => 'a',
                    'younger' => NULL,
                    'children' => [],
                    'depth' => 1,
                ]
            ),
            $tree->toArray()

        );

        $this->assertEquals($data, $tree->toTreeArr()['root']);

        $this->assertEquals(
            array (
                'root_0' =>
                    array (
                        'root_0_0' =>
                            array (
                                0 => 'root_0_0_0',
                            ),
                        0 => 'root_0_1',
                    ),
                0 => 'root_1',
            ),
            $tree->toOrderArr()['root']
        );

        $tree->destroy();


        $tree = new Tree();

        $tree->build($data, $rootName, '.');
        $this->assertEquals(
            [
                'root',
                'root.a',
                'root.a.b',
                'root.a.b.c',
                'root.a.d',
                'root.e',
            ],
            $tree->getBranchNames()
        );
        $this->assertEquals(
            [
                'root',
                'root_0',
                'root_0_0',
                'root_0_0_0',
                'root_0_1',
                'root_1',
            ],
            $tree->getBranchOrders()
        );
        $tree->destroy();
    }

}