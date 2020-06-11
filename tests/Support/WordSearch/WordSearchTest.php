<?php


namespace Commune\Test\Support\WordSearch;


use Commune\Support\WordSearch\Branch;
use Commune\Support\WordSearch\Tree;
use PHPUnit\Framework\TestCase;

class WordSearchTest extends TestCase
{


    public function testSearch()
    {
        $text = <<<EOF
啦啦哟哟哟哟
今天天气不错
挺风和日丽的
我们下午没有课
这的确挺爽的
我一大中午早早的跑去上自习
心里琢磨着大学生活是多么美好啊
这一眨眼的功夫我就进了主楼
要说俺们这嘎瘩自习室其实挺多的
可是你别说没有书包占座那就难找了
我是跋山涉水啊
翻山越岭啊
好不容易来到一个桃源深处
我这一推门我考,
真是豁然开朗
这XXXXX教室就真没多少人
说是迟那是快我就扔下书包
找了一个位置就坐了下去
我这一坐不要紧呐
差点成残疾
回头一瞅发现凳子根本没有板
我心说,得亏哥们原来我还练过
要不然还不被你整成肛裂啊
我心说,得亏哥们原来我还练过
要不然还不被你整成肛裂啊
上自习还得抓点紧
不是于是乎我就换了一个靠后门的座
门外走廊号啕大哭的歌手还挺多
他们唱得那个爱来爱去我就忍啊
但是推门进屋的同学怎么着不知道关门呢
你们来来回回的,这是走城门呢
不一会我周围就坐满了邻居也
可这屋里的气氛可就有点不对劲了
咋就不对劲呢
咋就不对劲呢
坐在我前面的是一对情侣
他们有说有笑啊有打有闹
根本没把我这电灯泡放在眼里(唉耶)
我心说算了(唉耶)还是少损两句
毕竟人家是纯洁的男女关系
后面那个兄弟他多少有点离谱
朗读发声训练做得的确刻苦
莫非他是传说中的说唱歌手
要不这嘴里怎么就唠唠叨叨没完
可是自言自语得有时有晌啊
你这叨咕叨咕的中国英语能不能改成默念
左面那个妹子她就十分乖巧
只是嘴里零食象造坑一样没完没了
这嘎嘎蹦蹦的花样还真不少
我心想大妹子几天没吃了?(真的)
右面的那个姑娘手机四十和弦(不错耶)
可是姐姐你的电话不能接起没完呐
一会的功夫你都跑出去五趟了
有啥话你不能一气说完呢
你看你旁边哪个大哥他就挺diao
业务也挺忙短信十多条
可是他XXX就不说给调成振动的
他XXX就不说给调成振动的
我说唉唉唉唉唉唉耶我去
我说这个屋里的我的兄弟姐妹们啊
你们真的真的是来上自习的吧?
不是我的冤家派来故意玩我的吧
不是我的冤家派来故意玩我的吧
这时候走进一个美丽的女孩
她盯着我,我的位置走了过来
我这心里扑腾扑腾得开始乱跳
她停在我面前说这个座位我已经占了
真是莫名其妙是不是她在搞笑
可是她竟从桌膛里变出一本书
书名没看清楚可是看清"占座"俩字
再看这位大小姐正得意的看着我
她得意的笑她得意的笑
那种感觉就像小样,你新来的吧
新新新新新来的吧我是新新新新新来的吧
我说妹子
我眼瞅就毕业的人了
对于你们这占座一族还是无可奈何
最后一声得还是发扬风格
打不起我我还躲不起么我
我躲躲躲躲躲躲躲我躲躲躲躲
我躲哪嘎哒去啊
只要功夫深(就是)
铁杵磨成针(就是)
皇天还不负我这样的自习有心人(就是)
最终我找到了一个安静的角落
心想终于终于可以看书了
忽然间一个黑影窜上了讲台
迅雷不及掩耳之势在黑板上写着
下午三点有会,谢谢合作
下午三点有会,谢谢合作
唉我一瞅表,我cao,
这不两点六十了吗
你们这帮家伙简直XXX强盗
我背起书包骂了一句very well
这就是俺们自习室俺们的学校
这就是老子们的自习室俺们的学校
走走走走走走走走走着瞧走走走走就走
我说这个自习室的兄弟姐妹们
你们真的是来上自习的吗
走走走你们真的是来上自习的吗
你们真的是上自习的吗你说这不扯的吗
EOF;

        $keys = [
            '天气' => 'a',
            '天晴' => 'b',
            '老子' => 'c',
            '老子们' => 'd',
            '打人' => 'e',
            '自习' => 'f',
        ];

        $root = new Branch('');
        foreach ($keys as $key => $value) {
            $root->buildBranches($key, $value);
        }

        // 尝试匹配
        $t = '天晴';
        $result = [];
        $root->search($t, 0 , $result);
        $this->assertEquals(['b' => 1], $result);

        $t = '天晴吗?';
        $result = [];
        $root->search($t, 0 , $result);
        $this->assertEquals(['b' => 1], $result);


        $len = mb_strlen($text);
        $result = [];

        for ( $i = 0 ; $i < $len ; $i++ ) {
            $root->search($text, $i, $result);
        }


        $this->assertEquals(
            [
                'a' => 1,
                'f' => 11,
                'c' => 1,
                // 没有 d, 因为 "老子" 命中后, 就不会命中 "老子们"
                // 'd' => 1,
            ],
            $result
        );
    }

    public function testSearchCity()
    {
        $text = file_get_contents(__DIR__ . '/city.txt');
        $cities = explode("\n", $text);
        $root = new Branch('');

        $infoCities = [];
        foreach ($cities as $city) {
            if (empty($city)) {
                continue;
            }
            $city = trim($city);
            $root->buildBranches($city, $city);

            $infoCities[$city] = $city;
        }


        // 测试
        $result = [];
        $input = '和田明天的天气如何';
        $root->search($input, 0 , $result);
        $this->assertArrayHasKey('和田', $result);

        $this->assertTrue($root->hasBranch("株"));
        $zhu = $root->getBranch("株");
        $this->assertTrue($zhu->hasBranch("洲"));
        $this->assertTrue($zhu->getBranch("洲")->hasValue(), '株洲 exists');



        // 测试
        $result = [];
        $input = '后天株洲是不是下雨';
        $length = mb_strlen($input);
        for ($i = 0 ; $i < $length ; $i ++ ) {
            $root->search($input, $i , $result);
        }

        $this->assertArrayHasKey('株洲', $result);

    }

    public function testTree()
    {
        $text = file_get_contents(__DIR__ . '/city.txt');
        $cities = explode("\n", $text);
        $infoCities = [];
        foreach ($cities as $city) {
            if (empty($city)) {
                continue;
            }
            $city = trim($city);
            $infoCities[$city] = $city;
        }

        // 用来测试先序匹配
        $infoCities['洲株'] = '洲株';

        $tree = new Tree($infoCities);
        $this->assertArrayHasKey('株洲', $tree->search('后天株洲是不是下雨'));

        $result =  $tree->search('明天西安不是下雨');
        $this->assertArrayHasKey('西安', $result);

        $this->assertArrayHasKey('株洲', $tree->search('后天株株洲株是不是下雨'));
        $this->assertArrayHasKey('洲株', $tree->search('后天洲株洲是不是下雨'));
        $this->assertArrayNotHasKey('洲株', $tree->search('后天株株洲株是不是下雨'));
    }

}