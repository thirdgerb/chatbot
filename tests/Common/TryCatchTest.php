<?php

/**
 * Class TryCatchTest
 * @package Commune\Chatbot\Test\Common
 */

namespace Commune\Chatbot\Test\Common;


use PHPUnit\Framework\TestCase;

class TryCatchTest extends TestCase
{
    protected $k = true;

    public function testFinally()
    {
        $this->assertEquals(123, $this->func());
        $this->assertFalse($this->k);

    }

    protected function func()
    {
        try {

            return 123;

        } catch (\Exception $e) {
            throw $e;

        } finally {
            $this->k = false;
        }
    }

}