<?php


namespace Commune\Components\Demo\Cases\Weather;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Callables\Actions\Talker;
use Commune\Chatbot\App\Intents\ActionIntent;
use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\NLU\Contracts\EntityExtractor;
use Commune\Support\Utils\StringUtils;
use Illuminate\Support\Arr;

/**
 * 询问天气的测试用例.
 *
 * 为了兼容各种情况, 使用了 NLU 的意图识别, 也使用了系统自带的 php 的extractor
 *
 * @property string $city 请告诉我您想查询的城市
 * @property string $date 请告诉我您想查询的日期, 可用英文, 或者Y-m-d的格式. 中文仅支持明天,后天等常用词
 */
class TellWeatherInt extends ActionIntent
{
    const DESCRIPTION = '查询天气测试用例';

    const SIGNATURE = 'tellWeather
        {city : 请问您想查询哪个城市的天气?}
        {date : 请问您想查询哪天的天气?}
    ';

    const CASTS = [
        'city' => 'string',
        'date' => 'string',
    ];

    public static function getContextName(): string
    {
        return StringUtils::normalizeContextName('demo.cases.tellweather');
    }

    public function __staging(Stage $stage) : void
    {
        $stage
            ->onFallback(Redirector::goFulfill())
            ->onIntended(Redirector::goFulfill());
    }

    public function __exiting(Exiting $listener): void
    {
        $listener->onFulfill(Talker::say()->info("结束天气查询"));
    }


    public function __hearing(Hearing $hearing) : void
    {
        $hearing
            ->isIntent(
                $this->getName(),
                function(Dialog $dialog, TellWeatherInt $intent){
                    $this->city = $intent->city;
                    $this->date = $intent->date;
                    return $dialog->restart();
                }
            )
            ->runAnyIntent()
            ->matchEntity(
                'city',
                function(NLU $nlu, Dialog $dialog){
                    $values = $nlu
                        ->getMatchedEntities()
                        ->get('city');

                    $this->city = $values;
                    return $dialog->restart();
                }
            )
            ->matchEntity(
                'date',
                function(NLU $nlu, Dialog $dialog) {
                    $this->date = $nlu
                        ->getMatchedEntities()
                        ->get('date');
                    return $dialog->restart();
                }
            );
    }


    public function action(Stage $stageRoute): Navigator
    {
        return $stageRoute->buildTalk()
            ->action(function(Dialog $dialog){

                $city = $this->parseCity($dialog, $this->city);
                if (empty($city)) {
                    unset($this->city);
                    $dialog->say()->info("对不起, 我们没有您说的城市信息");
                    return $dialog->goStage('more');
                }

                $date = $this->parseDate($dialog, $this->date);
                $time = $this->fetchTime($date);
                if (!isset($time)) {
                    $dialog
                        ->say()
                        ->warning("对不起, 日期没识别出来. 请考虑使用 '2019-10-01' 这样的格式");
                    unset($this->date);
                    return $dialog->goStagePipes(['date', 'start']);
                }

                $realDate = date('Y-m-d', $time);
                $this->broadcast($dialog, $city, $realDate);

                return $dialog->goStage('more');
            });
    }


    protected function broadcast(Dialog $dialog, string $city, string $realDate) : void
    {
        $dialog->say()->info(
            "(假装调用了api) %city%在%date%的天气是%weather%, 温度%temp%度 (纯属随机值, demo 示范用, 请勿当真)",
            [
                'temp' => rand(10, 40),
                'date' => $realDate,
                'city' => $city,
                'weather' => Arr::random([
                    '多云', '大雨', '狂风', '风和日丽', '雾霾', '沙尘暴',
                ])
            ]
        );

    }


    public function __onMore(Stage $stage) : Navigator
    {
        return $stage
            ->buildTalk()
            ->info('还需要了解更多吗?')
            ->wait()
            ->hearing()
            ->isPositive(function(Dialog $dialog){
                $this->city = null;
                $this->date = null;
                return $dialog->restart();
            })
            ->isNegative(Redirector::goFulfill())
            ->runDefaultFallback()
            ->end(Redirector::goFulfill());

    }

   /*---------- validate -----------*/

    protected function parseCity(Dialog $dialog, $city) : string
    {
        if (empty($city)) {
            return '';
        }

        $city = strval($city);
        /**
         * @var EntityExtractor $extractor
         */
        $extractor  = $dialog->app->make(EntityExtractor::class);
        $matches = $extractor->match($city, 'city');

        if (empty($matches)) {
            return '';
        }

        return current($matches);
    }

    protected function parseDate(Dialog $dialog, $date) : string
    {
        if (empty($date)) {
            return '';
        }
        $date = strval($date);

        /**
         * @var EntityExtractor $extractor
         */
        $extractor  = $dialog->app->make(EntityExtractor::class);
        $matches = $extractor->match($date, 'date');

        if (empty($matches)) {
            return '';
        }

        return current($matches);
    }

    protected function fetchTime(string $date) : ? int
    {
        if (empty($date)) {
            return null;
        }

        $word = $this->dateAliases[$date] ?? $date;
        $time = strtotime($word);

        return is_int($time) ? $time : null;
    }

}