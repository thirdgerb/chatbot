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

use Commune\Support\Markdown\MarkdownUtils;
use PHPUnit\Framework\TestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class MarkdownUtilsTest extends TestCase
{

    public function testMarkdownComments()
    {
        $line = '* hello';
        $parsed = MarkdownUtils::parseCommentLine($line);
        $this->assertNull($parsed);


        $line = '[//]: # (@test- hello world)';

        list($comment, $content) = MarkdownUtils::parseCommentLine($line);
        $this->assertEquals('test-', $comment);
        $this->assertEquals('hello world', $content);

        $line1 = '[//]:   #  ( @test hello world       )';
        list($comment, $content) = MarkdownUtils::parseCommentLine($line1);
        $this->assertEquals('test', $comment);
        $this->assertEquals('hello world', $content);


        $e = null;
        try {
            $line3 = "$line\n$line1";
            MarkdownUtils::parseCommentLine($line3);
        } catch (\InvalidArgumentException $e) {

        }
        $this->assertNotNull($e);

        $line4 = "$line1\n ";
        list($comment, $content) = MarkdownUtils::parseCommentLine($line4);
        $this->assertEquals('test', $comment);
        $this->assertEquals('hello world', $content);


        $line5 = "[//]: # (@break)";
        list($comment, $content) = MarkdownUtils::parseCommentLine($line5);
        $this->assertEquals('break', $comment);
        $this->assertEmpty($content);
    }

    public function testTitle()
    {
        $line = '[-_-] hello world';
        $this->assertNull(MarkdownUtils::parseTitle($line));

        $line = ' ## hello world  ';
        list($level, $title) = MarkdownUtils::parseTitle($line);
        $this->assertEquals(2, $level);
        $this->assertEquals('hello world', $title);

        $line = ' # hello world  ';
        list($level, $title) = MarkdownUtils::parseTitle($line);
        $this->assertEquals(1, $level);
        $this->assertEquals('hello world', $title);

    }

}