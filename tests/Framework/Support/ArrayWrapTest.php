<?php

/**
 * Class ArrayWrapTest
 * @package Commune\Chatbot\Test\Framework\Support
 */

namespace Commune\Chatbot\Test\Framework\Support;


use Commune\Chatbot\Framework\Support\ArrayWrapper;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class ArrayWrapTest extends TestCase
{
//
//    public function testOffsetSet()
//    {
//        $data = [
//            'a' => [
//                'b' => 'c'
//            ]
//        ];
//
//        $w = new ArrayWrapper($data);
//
//        $this->assertTrue($w['a'] instanceof ArrayWrapper);
//
//        $w['a']['b'] = 'd';
//
//        $this->assertEquals('d', $w->toArray()['a']['b']);
//
//    }

    public function testIterator()
    {
        $data = [
            'a' => [[1, 2], 2],
            'b' => [1, 2],
            'c' => [1, 2],
        ];

        $w = new ArrayWrapper($data);

        $this->assertEquals(3, count($w));

        $this->assertTrue($w['a'] instanceof ArrayWrapper);

        $c = $w['a'][0];
        $this->assertTrue($c instanceof ArrayWrapper);
        $this->assertEquals(1, $c[0]);

        foreach($data as $key => $val) {
            $this->assertEquals($data[$key], $val);
        }

    }
}