<?php

/**
 * Class ArrayCacheAdapterTest
 * @package Commune\Test\Chatbot\App\Cache
 */

namespace Commune\Test\Chatbot\App\Cache;


use Commune\Chatbot\App\Drivers\Demo\ArrayCache;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\ConversationLogger;
use PHPUnit\Framework\TestCase;

class ArrayCacheAdapterTest extends TestCase
{

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testCache()
    {
        $mocker = \Mockery::mock(Conversation::class);
        $mocker->expects('getLogger')->andReturn(\Mockery::mock(ConversationLogger::class));
        $mocker->expects('getTraceId')->andReturn('123');


        $cache = new ArrayCache($mocker);
        $psr = $cache->getPSR16Cache();

        $psr->set('test', '123');
        $this->assertTrue('123' === $psr->get('test'));
    }

}