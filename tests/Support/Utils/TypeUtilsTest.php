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

use Commune\Support\Utils\TypeUtils;
use PHPUnit\Framework\TestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TypeUtilsTest extends TestCase
{

    public function testPureListTypeHint()
    {
        $this->assertTrue(TypeUtils::isListTypeHint('string[]'));
        $this->assertEquals('string', TypeUtils::pureListTypeHint('string[]'));

        $this->assertFalse(TypeUtils::isListTypeHint('string'));
    }

    public function testValidate()
    {
        $this->assertTrue(TypeUtils::validate('string[]', ['abc', 'def']));
        $this->assertFalse(TypeUtils::validate('string[]', ['abc', 123], true));

        $this->assertTrue(TypeUtils::validate('string[][]', [
            ['abc', 'def'],
            ['b', 'c'],
        ]));

        $this->assertFalse(TypeUtils::validate('string[][]', [
            ['abc', 'def'],
            'd'
        ]));
    }

    public function testValidateInt()
    {
        $this->assertFalse(TypeUtils::validate('int', 1.1));
    }

    public function testListValidate()
    {
        $this->assertTrue(TypeUtils::listValidate('string', ['abc', 'def']));
        
    }
    
    public function testStrict()
    {
        $this->assertFalse(TypeUtils::listValidate('string', ['abc', 123], true));

        $this->assertTrue(TypeUtils::listValidate('string', ['abc', 123], false));
    }

    public function testStringListValidate()
    {
        $s = '北京|上海|重庆|郑州|洛阳|焦作|商丘|信阳|周口|鹤壁|安阳|濮阳|驻马店|南阳|开封|漯河|许昌|新乡|济源|灵宝|偃平顶山|卫辉|辉县|舞钢|新密|孟州|沁阳|郏县|合肥|亳州|芜湖|马鞍山|池州|黄山|滁州|安庆|淮南|淮北|蚌埠|宿州|宣城|六安|阜阳|龙海|建瓯|武夷山|长乐|福清|晋江|南安|福安|邵武|石狮|福鼎|建阳|漳平|永安|兰州|白银|武威|金昌|平凉|张掖|嘉峪关|酒泉|庆阳|毕节|清镇|铜仁|赤水|仁怀|福泉|海口|三亚|万宁|文昌|儋州|琼海|东方|五指山|石家庄|保定|唐山|邯郸市邢台|沧州|衡水|廊坊|承叶城|辛集|涿州|定州|晋州|霸州|黄骅|遵化|张家口|沙河|三河|冀州|武安|河间市深州|新乐|泊头|安国|双滦区|高碑店|哈尔滨|伊春安|五大连池|阿城|尚志|五常|安达|七台河|绥芬河|双城|海伦|宁安|讷河|穆棱|同江|肇东|武汉|荆门|咸宁|襄阳|荆州|黄石|宜昌|随松滋|丹江口|武穴|广水|石首市大冶|枝江|应城|宜城|当阳|安陆|宜都|利川|长沙|郴州|益阳|娄底|株洲|衡阳|湘潭|岳阳|常德|邵阳|冈|韶山|湘西州|长春|吉林|通化|白城|四平|辽源|松原|白山|集安|梅河口|双辽|延吉|九台|桦甸|榆树|蛟河|磐石|大安|德惠|洮南|龙主岭|图们|舒兰|和龙|临江|敦化|南京|无锡|常州|扬州|徐州|苏州|连云港|盐城|淮安|宿迁|镇江|南通|泰州|兴化|东台|常熟|江阴|张|仪征|太仓|姜堰|高邮|金坛|句容|灌南县|南昌|赣州|上饶|宜春|景德镇|新余|九江|萍乡|抚州|鹰潭|吉安|丰城|樟树|德兴|瑞金|井冈山|铁岭|本溪|丹东|抚顺|锦州|辽阳|阜新|调兵山|朝阳|海城|北票|盖州|凤城|庄河|凌源|开原|兴城|新民|大石桥|东港|北宁|瓦房店台|淄博|枣庄|泰安|临沂|日照|德州|聊城|滨州|乐陵|兖州|诸城|邹城|滕州|肥城|新泰|胶州|胶南|即墨|龙口|平度|莱西|太原|大同|中|运城|忻州|朔州|吕梁|古交|高平|永济|孝义|侯马|霍州|介休|河津|汾阳|原平|晋城|潞城|西安|咸阳|榆林|宝鸡|铜川|渭南|汉中|贡|泸州|广元|达州|资阳|绵阳|眉山|遂宁|雅安|阆中|攀枝花|广汉|绵竹|万源|华蓥|江油|西昌|彭州|简阳|崇州|什邡|峨眉山|邛崃|双|宁波|绍兴|温州|台州|湖州|嘉兴|金华|舟山|衢州|丽水|余姚|乐清|临海|温岭|永康|瑞安|慈溪|义乌|上虞|诸暨|海宁|桐乡|兰溪|龙深圳|珠海|汕头|佛山|韶关|湛江|肇庆|江门|茂名|惠州|梅州|汕尾|河源|阳江|清远|东莞|中山|潮州|揭阳|云浮|南宁|贺州|玉林|桂林百色|防城港|贵港|河池|崇左|来宾|东兴|桂平|北流|岑溪|合山|凭祥|宜州|呼和浩特|呼伦贝尔|赤峰|扎兰屯|鄂尔多斯|乌兰察布|巴彦临河|丰镇|通辽|银川|固原|石嘴山|青铜峡|中卫|吴忠|灵武|拉萨|那曲|山南|林芝|昌都|阿里地区日喀则|乌鲁木齐|石河子|喀什';

        $strs = explode('|', $s);

        $this->assertTrue(TypeUtils::validate('string[]', $strs));

    }


}