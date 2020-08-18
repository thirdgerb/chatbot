<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\NLU;

use Commune\NLU\Jieba\JiebaOption;
use Commune\NLU\Jieba\JiebaTokenizer;
use PHPUnit\Framework\TestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class JiebaTestT extends TestCase
{
    protected $run = false;

    /**
     * @var JiebaTokenizer
     */
    protected $tokenizer;

    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        if (!$this->run) return;

        $this->tokenizer = new JiebaTokenizer(
            JiebaTokenizer::defaultOption(),
            new JiebaOption()
        );
    }

    public function testTokenize()
    {
        if (!$this->run) return;

        $words = $this->tokenizer->tokenize("首先，我们看下什么是停止词。停止词，是由英文单词:stop word翻译过来的，原来在英语里面会遇到很多a，the，or等使用频率很多的字或词，如果搜索引擎要将这些词都索引的话，那么几乎每个网站都会被索 引，也就是说工作量巨大。可以毫不夸张的说句，只要是个英文网站都会用到a或者是the。那么这些英文的词跟我们中文有什么关系呢？", ['呢', '的']);

        $this->assertFalse(in_array('呢', $words));
        $this->assertFalse(in_array('的', $words));
        $this->assertTrue(in_array('我们', $words));
        $this->assertTrue(in_array('什么', $words));

        $words = $this->tokenizer->tokenize("首先，我们看下什么是停止词。停止词，是由英文单词:stop word翻译过来的，原来在英语里面会遇到很多a，the，or等使用频率很多的字或词，如果搜索引擎要将这些词都索引的话，那么几乎每个网站都会被索引，也就是说工作量巨大。可以毫不夸张的说句，只要是个英文网站都会用到a或者是the。那么这些英文的词跟我们中文有什么关系呢？", null);

        $this->assertTrue(is_array($words));

    }
}