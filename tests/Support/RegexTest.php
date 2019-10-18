<?php


namespace Commune\Test\Support;


use Commune\Components\Predefined\Intents\Attitudes\AffirmInt;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{

    public function testAffirm()
    {
        $p = AffirmInt::REGEX[0][0];
        $this->assertEquals(1, preg_match($p, '是'));
        $this->assertEquals(1, preg_match($p, '好'));
        $this->assertEquals(1, preg_match($p, '对'));


    }

}