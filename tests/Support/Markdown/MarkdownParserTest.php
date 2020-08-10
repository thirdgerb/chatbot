<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Support\Markdown;

use Commune\Support\Markdown\Data\MDSectionData;
use Commune\Support\Markdown\Parser\IMDParser;
use PHPUnit\Framework\TestCase;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class MarkdownParserTest extends TestCase
{
    public function testParse()
    {
        $content = $this->getContent();
        $parser = IMDParser::parse('test', 'root', $content);
        $parsedMD = $parser->toMarkdown();

        $parser2 = IMDParser::parse('test', 'root', $parsedMD);
        $this->assertEquals($parsedMD, $parser2->toMarkdown());

        $nameArr = array_map(function(MDSectionData $data) {
            return $data->name;
        }, $parser->getSections());

        $this->assertTrue(in_array("title~1~3", $nameArr));

        $nameArr2 = array_map(function(MDSectionData $data) {
            return $data->name;
        }, $parser2->getSections());

        $this->assertEquals($nameArr, $nameArr2);


        $map = [];
        foreach ($parser2->getSections() as $orderId => $section) {
            $map[$orderId] = $section->title;
        }

        $this->assertEquals(
            [
                // 根节点为空
                'root' => '',
                // 序号都大1
                'root_0' => 'title 1',
                'root_0_0' => 'title 1.1',
                'root_0_1' => 'title 1.2',
                'root_0_1_0' => 'title 1.2.1',
                'root_0_1_1' => 'title 1.2.2',
                'root_0_2' => 'title 1.3',
            ],
            $map
        );

        $comments = $parser2->getSections()['root_0']->comments;

        $this->assertEquals(
            [
                'parser' => [
                    '@name',
                ]
            ],
            $comments
        );

        $ids = array_map(function(MDSectionData $data) {
            return $data->id;
        }, $parser2->getSections());

        $this->assertEquals(
            [
                'root' => 'test.root',
                'root_0' => 'test.root_0',
                'root_0_0' => 'test.root_0_0',
                'root_0_1' => 'test.root_0_1',
                'root_0_1_0' => 'test.root_0_1_0',
                'root_0_1_1' => 'test.root_0_1_1',
                'root_0_2' => 'test.root_0_2',
            ],
            $ids
        );
    }

    protected function getContent()
    {
        return <<<EOF

[intent]: intentName
[title]: title
[desc]: desc

[name]: test

测试前缀内容.

# title 1

测试

[empty]: empty
[title]: 注释一定要有内容


测试其它的文本, 包括 ```abc```

正确的 parser 位置

[parser]: @name

* list
* list2
[parser]: never
* list3

[name]: title1

## title 1.1

测试
换行的文本内容


测试文本中用 [link](link) 看看会怎么样
是什么结构呢?

* 1
* 2
* 3

## title 1.2

测试

### title 1.2.1

测试

### title 1.2.2

测试

## title 1.3

测试

[name]: title~1~3

EOF;

    }
}