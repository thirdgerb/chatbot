<?php


namespace Commune\Test\Support\Utils;


use Commune\Support\Utils\StringUtils;
use PHPUnit\Framework\TestCase;

/**
 * @property int $abc
 * @property string $abe sdkjfksjdf
 * @property     $bbbb sdjf aksjdfks skd
 */
class StringUtilsTest extends TestCase
{

    /**
     * 用单字符表示命名空间的情况下, 拆分前缀和最后一个单位.
     */
    public function testDividePrefixAndName()
    {
        $this->assertEquals(
            ['skjdfkakf.ksjd.jdfj', 'kjfd'],
            StringUtils::dividePrefixAndName('skjdfkakf.ksjd.jdfj.kjfd')
        );

        $this->assertEquals(
            ['skjdfkakf\\ksjd\\jdfj', 'kjfd'],
            StringUtils::dividePrefixAndName('skjdfkakf\\ksjd\\jdfj\\kjfd', '\\')
        );

        $this->assertEquals(
            ['', 'skjdfkakfksjdjdfjkjfd'],
            StringUtils::dividePrefixAndName('skjdfkakfksjdjdfjkjfd', '\\')
        );

        $this->assertEquals(
            'abc.efg.hij.k',
            StringUtils::gluePrefixAndName('abc.efg.hij', 'k')
        );
    }


    /**
     * @description  123 测试一下. 试测, 试吃.二十世纪东方隆
jksdflkjsldkjflsdjkf
lksjdflskjdlfjskdfjlsdjkfslkjdflskjdflkjsfsd*
     *
     */
    public function testFetchDescAnnotation()
    {
        $r = new \ReflectionClass(static::class);
        $doc = $r->getMethod(__FUNCTION__)->getDocComment();

        $desc = StringUtils::fetchDescAnnotation($doc);

        $this->assertEquals('123 测试一下. 试测, 试吃.二十世纪东方隆
jksdflkjsldkjflsdjkf
lksjdflskjdlfjskdfjlsdjkfslkjdflskjdflkjsfsd', $desc);
    }


    public function testFetchPropertyAnnotations()
    {
        $r = new \ReflectionClass(static::class);
        $doc = $r->getDocComment();

        $properties = StringUtils::fetchPropertyAnnotations($doc);

        $this->assertEquals(['abc', ''], $properties[0]);
        $this->assertEquals(['abe', 'sdkjfksjdf'], $properties[1]);
        $this->assertEquals(['bbbb', 'sdjf aksjdfks skd'], $properties[2]);

    }

    public function testMatchNameAndMethod()
    {

        list($name, $method) = StringUtils::matchNameAndMethod('abc.efg@ke_12');

        $this->assertEquals('abc.efg', $name);
        $this->assertEquals('ke_12', $method);

        $this->assertNull(StringUtils::matchNameAndMethod('abc@'));
        $this->assertNull(StringUtils::matchNameAndMethod('1abc@hhh'));
    }

    public function testHasAnnotation()
    {
        $doc1 = <<<EOF
    /**
     * test stage
     * @stage
     */
EOF;
        $doc2 = <<<EOF
    /**
     * test stage
     * @stage testing
     */
EOF;

        $doc3 = <<<EOF
    /**
     * test stage
     * @property test
     */
EOF;

        $this->assertTrue(StringUtils::hasAnnotation($doc1, 'stage'));
        $this->assertTrue(StringUtils::hasAnnotation($doc2, 'stage'));
        $this->assertTrue(StringUtils::hasAnnotation($doc3, 'property'));
        $this->assertFalse(StringUtils::hasAnnotation($doc3, 'stage'));
        $this->assertFalse(StringUtils::hasAnnotation($doc3, 'prop'));

    }

    public function testNormalize()
    {
        $this->assertEquals('0', StringUtils::normalizeString('零.'));

        $this->assertEquals('00', StringUtils::normalizeString('零零.'));

        $this->assertEquals('第十2个', StringUtils::normalizeString('第十二个'));
    }


    public function testDotPathParser()
    {
        $this->assertEquals(
            'intro.chat.chat-interface',
            StringUtils::dotPathParser('intro.chat', '.chat.chat-interface')
        );

        $this->assertEquals(
            'intro.chat.*',
            StringUtils::dotPathParser('intro.chat', '.chat.*')
        );

        $this->assertEquals(
            'hello',
            StringUtils::dotPathParser('chat', '.hello')
        );

        $this->assertEquals(
            'chat.hello',
            StringUtils::dotPathParser('chat.abc', '.hello')
        );

        $this->assertEquals(
            'hello',
            StringUtils::dotPathParser('chat.abc', 'hello')
        );
    }


    public function testContextNameValidate()
    {
        $this->assertTrue(StringUtils::validateDefName('a.b0'));

        // 允许中间线
        $this->assertTrue(StringUtils::validateDefName('a-b0'));

        // 不支持大写
        $this->assertFalse(StringUtils::validateDefName('A.b0'));

        // 下划线也不允许
        $this->assertFalse(StringUtils::validateDefName('a_b0'));

    }


    public function testPropertiesAnnotations()
    {
        $a = '/**
         * @property string $hello
         * @property string[]|int $hello1
         * @property-read string[]|int $hello2
         * @property-write string $hello3 dskjdlfjkskdjf
         * @property $hello4
         */';

        $matched = StringUtils::fetchVariableAnnotationsWithType($a);
        $this->assertEquals(
            array (
                array (
                    0 => 'hello',
                    1 => 'string',
                    2 => '',
                ),
                array (
                    0 => 'hello1',
                    1 => 'string[]|int',
                    2 => '',
                ),
                array (
                    0 => 'hello2',
                    1 => 'string[]|int',
                    2 => '',
                ),
                array (
                    0 => 'hello3',
                    1 => 'string',
                    2 => 'dskjdlfjkskdjf',
                ),
                array (
                    0 => 'hello4',
                    1 => '',
                    2 => '',
                )
            ),
            $matched
        );
        $matched = StringUtils::fetchVariableAnnotationsWithType($a,'@property', true);
        $this->assertEquals(
            array (
                array (
                    0 => 'hello',
                    1 => 'string',
                    2 => '',
                ),
                array (
                    0 => 'hello1',
                    1 => 'string[]|int',
                    2 => '',
                ),
                array (
                    0 => 'hello4',
                    1 => '',
                    2 => '',
                )
            ),
            $matched
        );

    }


    public function testMatchKeywords()
    {
        $text = "今天天气不错, 数字1234, I am so depressing";

        $this->assertTrue(StringUtils::expectKeywords($text, ['天气', 'am', '123']));
        $this->assertTrue(StringUtils::expectKeywords($text, ['天气', ['am', '567']]));

        $this->assertFalse(StringUtils::expectKeywords($text, ['天气', 'am', '567']));
        $this->assertTrue(StringUtils::expectKeywords($text, ['天气', 'am', '567'], false));

        $this->assertFalse(StringUtils::expectKeywords($text, [['am', '567']], false));
    }


    /**
     * @intent intentNameAlias
     * @example intentExample1
     * @example intentExample2
     * hello world
     *
     * @regex /^regex1$/
     * @regex /^regex2$/
     * @signature commandName {arg1} {arg2}
     *
     */
    public function testFetchAnnotation()
    {
        $r = new \ReflectionMethod(static::class, __FUNCTION__);
        $doc = $r->getDocComment();

        $this->assertEquals(
            'intentNameAlias',
            StringUtils::fetchAnnotation($doc, 'intent')[0]
        );

        $this->assertEquals(
            ['intentExample1', 'intentExample2'],
            StringUtils::fetchAnnotation($doc, 'example')
        );


        $this->assertEquals(
            ['/^regex1$/', '/^regex2$/'],
            StringUtils::fetchAnnotation($doc, 'regex')
        );

        $this->assertEquals(
            'commandName {arg1} {arg2}',
            StringUtils::fetchAnnotation($doc, 'signature')[0]
        );

    }
}