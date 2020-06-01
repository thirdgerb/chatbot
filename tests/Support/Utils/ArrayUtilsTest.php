<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Support\Utils;

use Commune\Support\Utils\ArrayUtils;
use PHPUnit\Framework\TestCase;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ArrayUtilsTest extends TestCase
{


    public function testExpectTokens()
    {
        $tokens = [ 'a', 'b', 'c',];

        $this->assertTrue(ArrayUtils::expectTokens($tokens, ['a', 'b', 'c']));
        $this->assertTrue(ArrayUtils::expectTokens($tokens, ['a', 'c']));
        $this->assertTrue(ArrayUtils::expectTokens($tokens, ['b', 'd'], false));
        $this->assertTrue(ArrayUtils::expectTokens($tokens, ['a', ['b', 'd'], 'c']));


        $this->assertFalse(ArrayUtils::expectTokens($tokens, ['a', 'c', 'd']));
        $this->assertFalse(ArrayUtils::expectTokens($tokens, ['a', 'b', 'c', 'd']));
        $this->assertFalse(ArrayUtils::expectTokens($tokens, ['a', ['e', 'f', 'g']]));


        $this->assertTrue(ArrayUtils::expectTokens($tokens, ['a', 'b', 'c'], false));

        $this->assertTrue(ArrayUtils::expectTokens($tokens, ['a', 'c'], false));
        $this->assertTrue(ArrayUtils::expectTokens($tokens, ['a', 'd'], false));
        $this->assertTrue(ArrayUtils::expectTokens($tokens, ['d', ['b', 'c']], false));


        $this->assertFalse(ArrayUtils::expectTokens($tokens, [['a', 'f'], ['b', 'g']], false));

        $this->assertFalse(ArrayUtils::expectTokens($tokens, ['d', ['b', 'e']], false));
    }

    public function testTransNameArrToListNames()
    {
        $names = [ 1, 1, 3, 3, 4, 7, 9, 8, 5, 3, 2, 7];

        $this->assertEquals(
            ['1[]', '3[]', '4', '7[]', '9', '8', '5', '2'],
            ArrayUtils::uniqueValuesWithListMark($names)
        );
    }

    public function testUniqueValuesWithListMarkByValueCounts()
    {
        $tests = [
            [ 1, 1, 2, 3],
            [2, 3, 4, 5],
            [3, 2, 1, 4],
            [6, 7, 6, 5],
        ];


        $valueCounts = array_reduce(
            $tests,
            function(array $valueCounts, $values) {
                $counts = ArrayUtils::valueCount($values);
                return ArrayUtils::mergeMapByMaxVal($valueCounts, $counts);
            },
            []
        );

        $uniques = ArrayUtils::uniqueValuesWithListMarkByValueCounts($valueCounts);
        $this->assertEquals(['1[]', '2', '3', '4', '5', '6[]', '7'], $uniques);
    }

    public function testParseValuesByKeysWithListMark()
    {
        $keys = ['a', 'b[]', 'c', 'd[]'];

        $values = [
            'a' => 123,
            'b' => [1, 2, 3],
            'c' => [1, 2, 3],
            'd' => 123,
            'e' => 321
        ];

        $actual = ArrayUtils::parseValuesByKeysWithListMark($values, $keys, true);
        $this->assertEquals(
            ['a' => 123, 'b' => [1,2,3], 'c' => 1, 'd' => [123]],
            $actual
        );

        $actual = ArrayUtils::parseValuesByKeysWithListMark($values, $keys, false);

        $this->assertEquals(321, $actual['e']);
    }

}