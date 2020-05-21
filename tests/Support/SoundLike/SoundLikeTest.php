<?php

/**
 * Class SoundLikeTest
 * @package Commune\Test\Support\SoundLike
 */

namespace Commune\Test\Support\SoundLike;


use Commune\Container\Container;
use Commune\Support\SoundLike\PinyinParser;
use Commune\Support\SoundLike\SoundLikeInterface;
use Commune\Support\SoundLike\SoundLikeManager;
use Overtrue\Pinyin\MemoryFileDictLoader;
use Overtrue\Pinyin\Pinyin;
use PHPUnit\Framework\TestCase;

class SoundLikeTest extends TestCase
{

    public function testSoundLike()
    {
        $parser = new PinyinParser(new Pinyin(MemoryFileDictLoader::class));
        $container = new Container();

        $manager = new SoundLikeManager($container);
        $manager->registerParser(SoundLikeManager::ZH, $parser);
        $result1 = $manager->soundLike(
            '测试 english 和数字1, 加符号;',
            '测试english和 数字1加符号'
        );

        $result2 = $manager->soundLike(
            '测试 english 和数字一, 加符号;',
            '测试english和 数字1加符号'
        );

        $result3 = $manager->soundLike(
            '测试部分匹配',
            '测试部分'
        );


        $result4 = $manager->soundLike(
            '测试部分',
            '测试部分匹配'
        );


        $result5 = $manager->soundLike(
            '测试部分',
            '测试部分匹配',
            SoundLikeInterface::COMPARE_START_WITH
        );

        $result6 = $manager->soundLike(
            '测试部分',
            '测试部分匹配',
            SoundLikeInterface::COMPARE_END_WITH
        );

        $result7 = $manager->soundLike(
            '分匹配',
            '测试部分匹配',
            SoundLikeInterface::COMPARE_END_WITH
        );

        $this->assertTrue($result1);
        $this->assertTrue($result2);
        $this->assertFalse($result3);
        $this->assertFalse($result4);
        $this->assertTrue($result5);
        $this->assertFalse($result6);
        $this->assertTrue($result7);

    }

}