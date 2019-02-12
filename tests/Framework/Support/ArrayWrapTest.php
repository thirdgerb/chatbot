<?php

/**
 * Class ArrayWrapTest
 * @package Commune\Chatbot\Test\Framework\Support
 */

namespace Commune\Chatbot\Test\Framework\Support;


use Commune\Chatbot\Framework\Support\ArrayWrapper;
use PHPUnit\Framework\TestCase;

class ArrayWrapTest extends TestCase
{

    public function testOffsetSet()
    {
        $data = [
            'a' => [
                'b' => 'c'
            ]
        ];

        $w = new ArrayWrapper($data);

        $this->assertTrue($w['a'] instanceof ArrayWrapper);

        $w['a']['b'] = 'd';

        $this->assertEquals('d', $w->toArray()['a']['b']);

    }

}