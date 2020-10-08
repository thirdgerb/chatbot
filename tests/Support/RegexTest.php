<?php

namespace Commune\Test\Support;

use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    public function testPregReplace()
    {
        $t = "abc\ndef\n\nhij\n\n\nklmn\n";
        $r = preg_replace('/\n+/', "\n\n", $t);
        $this->assertEquals("abc\n\ndef\n\nhij\n\nklmn\n\n", $r);
    }

    public function testAffirm()
    {
        $p = '/^(是|好|对|y|1)$/';
        $this->assertEquals(1, preg_match($p, '是'));
        $this->assertEquals(1, preg_match($p, '好'));
        $this->assertEquals(1, preg_match($p, '对'));
    }

    public function testTime()
    {
        $p = '/^[0-9]{2}:[0-9]{2}$/';

        $this->assertEquals(1, preg_match($p, '00:01'));
        $this->assertEquals(1, preg_match($p, '10:11'));
        $this->assertEquals(0, preg_match($p, '10::11'));
        $this->assertEquals(0, preg_match($p, 'a0:11'));
        $this->assertEquals(0, preg_match($p, '10:111'));

    }

}