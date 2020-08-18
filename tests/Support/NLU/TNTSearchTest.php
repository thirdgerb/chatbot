<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Support\NLU;

use PHPUnit\Framework\TestCase;
use TeamTNT\TNTSearch\Classifier\TNTClassifier;
use Fukuball\Jieba\Jieba;
use Fukuball\Jieba\Finalseg;
use TeamTNT\TNTSearch\Support\TokenizerInterface;

ini_set('memory_limit', '750m');

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TNTSearchTest extends TestCase
{
    // 单测开关. 需要占用 750 mb 左右内存.
    protected $run = false;

    public function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        if (!$this->run) return;
        Jieba::init();
        Finalseg::init();
    }

    public function testJieba()
    {
        if (!$this->run) return;

        $cut = Jieba::cut("今天天气不错, 挺风和日丽的");

        $this->assertEquals(
            ['今天天气', '不错', '挺', '风和日丽', '的'],
            $cut
        );
    }
    protected function getJiebaTokenizer() : TokenizerInterface
    {
        return new class implements TokenizerInterface {
            public function tokenize($text, $stopwords = [])
            {
                $words = Jieba::cut($text);
                return $words;
            }
        };
    }

    public function testLearn()
    {
        if (!$this->run) return;

        $intents = $this->getIntents();
        $classifier = new TNTClassifier();
        $classifier->tokenizer = $this->getJiebaTokenizer();

        foreach ($intents as $intent => $example) {
            $lines = explode("\n", $example);
            foreach ($lines as $line) {
                $classifier->learn($line, $intent);
            }
        }

        $guess = $classifier->predict("长沙明天天气如何");
        $this->assertEquals('demo.cases.tellweather', $guess['label']);


        $guess = $classifier->predict("做的还可以");
        $this->assertEquals('attitude.complement', $guess['label']);

        $guess = $classifier->predict("会刮风吗");
        $this->assertEquals('demo.cases.tellweather', $guess['label']);
    }

//    public function testFullTextSearch()
//    {
//        if (!$this->run) return;
//
//        $tnt = new TNTSearch();
//        $tnt->loadConfig([
//            'driver'    => 'mysql',
//            'host'      => 'localhost',
//            'database'  => 'dbname',
//            'username'  => 'user',
//            'password'  => 'pass',
//            'storage'   => __DIR__,
//        ]);
//        $tnt->tokenizer = $this->getJiebaTokenizer();
//        $index = $tnt->createIndex('intent');
//        $index->setPrimaryKey('intent');
//        $data = $this->getIntents();
//
//        $i = 0;
//        foreach ($data as $intent => $content) {
//            $i ++ ;
//            $index->insert(['id' => $i, 'intent' => $intent, 'content' => $content]);
//        }
//
//        $r = $tnt->search('北京', 10);
//        var_dump($r);
//    }


    protected function getIntents() : array
    {
        return [
                'attitude.affirm' => <<<EOF
yes
是的
没错
的确如此
是
就是这样
对的
对
嗯
嗯呢
EOF
                ,
                'attitude.complement' => <<<EOF
挺好的
真棒
你好牛啊
帅气
做的不错
点个赞
真不错
好厉害
厉害呀
真是好
very good
EOF
                ,

                'attitude.diss' => <<<EOF
太蠢了
你真是傻
笨蛋啊
傻逼吗
太垃圾了
你怎么这么傻
好蠢啊
你是智障吗
没见过这么蠢的
简直是个智障
好弱智啊
EOF
                ,
                'demo.cases.tellweather' => <<<EOF
今天天气怎么样
我想知道明天的天气如何
北京后天什么天气啊
气温如何
明天多少度
后天什么天气啊
上海大后天下雨吗
您知道广州的天气吗
会有暴风雨吗
请问明天下雨吗
后天多少度啊
明天是晴天吗
长沙下雨了吗
明天北京什么气温
深圳天气
上海天气
洛阳气温
EOF
                ,

            ];
    }

}