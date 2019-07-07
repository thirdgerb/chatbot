<?php


namespace Commune\Demo\App\Cases\Wheather;


use Commune\Chatbot\App\Intents\ActionIntent;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Illuminate\Support\Arr;

/**
 * @property string $city
 * @property string $date
 */
class TellWeatherInt extends ActionIntent
{
    const DESCRIPTION = '查询天气测试用例';

    const SIGNATURE = 'tellWeather
        {city : 请问您想查询哪个城市的天气?}
        {date : 请问您想查询哪天的天气?}
    ';

    // 给NLU用的例句.
    const EXAMPLES = [
        '[今天](date)天气怎么样?',
        '我想知道[明天](date)的天气如何',
        '[北京](city)[后天](date)什么天气啊',
        '气温如何?',
        '[上海](city)[大后天](date)下雨吗?',
        '你知道[广州]的天气吗',
        '请问[明天](date)下雨吗',
        '[后天](date)多少度啊?',
        '[明天](date)是晴天吗?',
        '[长沙](date)下雨了吗?',
    ];

    protected $cities = [
        '北京', '上海', '长沙', '广州', '西安', '洛阳',
    ];

    protected $wowCities = [
        '暴风城', '奥格瑞玛', '雷霆崖', '铁炉堡',
    ];

    protected $wowRuin = [
        '达纳苏斯', '幽暗城',
    ];

    protected $dateAliases = [
        '今天' => 'today',
        '昨天' => 'yesterday',
        '明天' => 'tomorrow',
        '后天' => '+2 day',
        '大后天' => '+3 day',
    ];

    public function action(Stage $stageRoute): Navigator
    {
        return $stageRoute->buildTalk()
            ->action(function(Dialog $dialog){

                $city = $this->city;

                if (! $this->doValidateCity($dialog, $city)) {
                    return $dialog->fulfill();
                }

                $date = $this->date;
                $time = $this->fetchTime($date);
                if (!isset($time)) {
                    $dialog->say()
                        ->warning("sorry, 日期 $date 不知道是哪天...请再告诉我一次?");
                    unset($this->date);
                    return $dialog->goStagePipes(['date', 'start']);
                }

                $realDate = date('Y-m-d', $time);

                $say = $dialog->say([
                    'temp' => rand(10, 40),
                    'date' => $realDate,
                    'city' => $city,
                    'weather' => Arr::random([
                        '多云', '大雨', '狂风', '风和日丽', '雾霾', '沙尘暴',
                    ]),
                ]);

                $say->info(" (为省工作量, 假装调用了api)");

                if (in_array($city, $this->wowRuin)) {
                    $say->info(
                        "%city% 已经被摧毁了, 我个人猜测%date%的天气是%weather%, 温度%temp%度");
                } elseif (in_array($city, $this->wowCities)) {
                    $say->info(
                        "%city%在%date%的天气是%weather%, 温度%temp%度, 不信你进游戏里看");
                } else {
                    $say->info(
                        "%city%在%date%的天气是%weather%, 温度%temp%度 (纯属随机值)");
                }

                $say->info("播报完毕!");
                return $dialog->fulfill();
            });
    }


    public function __exiting(Exiting $listener): void
    {
    }


    public function __validateCity(Dialog $dialog, Message $message) : ? Navigator
    {
        $text = $message->getTrimmedText();
        return $this->doValidateCity($dialog, $text) ? null : $dialog->repeat();
    }

    protected function doValidateCity(Dialog $dialog, string $city) : bool
    {
        $cities = array_merge($this->wowCities, $this->cities, $this->wowRuin);

        if (in_array($city, $cities)) {
            return true;
        }

        $dialog->say()->warning(
            "sorry, 我们现在只有以下城市的数据:" . implode(',', $cities)
        );

        return false;
    }

    public function __askDate(Dialog $dialog) :  void
    {
        $dialog->say()->askVerbose(
            '请告诉我您想查询的日期, 可用英文, 或者Y-m-d的格式. 中文仅支持明天,后天等常用词',
            [
                '今天',
                '明天',
                '后天'
            ]
        );
    }

    public function __validateDate(Dialog $dialog, Answer $message) : ? Navigator
    {
        $date = $message->toResult();
        $time = $this->fetchTime($date);

        if (isset($time)) {
            return null;
        }

        $dialog->say()->warning('对不起, 日期格式我无法理解...');

        return $dialog->repeat();
    }

    protected function fetchTime(string $date) : ? int
    {
        $word = $this->dateAliases[$date] ?? $date;
        $time = strtotime($word);

        return is_int($time) ? $time : null;
    }

}