<?php

/**
 * Class ContextCfgTest
 * @package Commune\Chatbot\Test\Framework\Context
 */

namespace Commune\Chatbot\Test\Framework\Context;


use Commune\Chatbot\Demo\Configure\ContextCfg\Root;
use PHPUnit\Framework\TestCase;

class ContextCfgTest extends TestCase
{

    public function testGetScopeTypes()
    {
        $c = new Root();
        $this->assertEquals(Root::SCOPE, $c->getScopeTypes());
    }

}