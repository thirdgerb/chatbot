<?php


namespace Commune\Components\Demo\Cases\Questionnaire;


use Carbon\Carbon;
use Commune\Chatbot\App\Memories\Questionnaire;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Session\Scope;

/**
 * @property int $score
 * @property Carbon $createdAt
 * @property Carbon|null $finishAt
 * @property-read int $during
 */
class ReadPersonality extends Questionnaire
{
    const DESCRIPTION = '问卷:15秒读出您的性格';

    const SCOPE_TYPES = [Scope::USER_ID];

    public static function __depend(Depending $depending): void
    {
    }

    protected function init(): array
    {
        return  parent::init() + [
            'finishAt' => null,
            'createdAt' => new Carbon(),
            'score' => 0,
        ];
    }

    protected function doStart(Stage $stage): Navigator
    {
        return $stage->buildTalk()->action(function (Dialog $dialog) : Navigator {
            $dialog->say()->info("这里是趣味测试题的测试用例.");

            if ($this->finish) {
                $dialog->say()->info(
                    '您于 '
                    . $this->createdAt->format('Y-m-d H:i:s')
                    . ' 已经测过. 结果如下:'
                );
                $this->showResult($dialog);
                return $dialog->goStage('retest');
            }

            if ($this->questionNumber > 0) {
                $dialog->say()->info(
                    '您于 '
                    . $this->createdAt->format('Y-m-d H:i:s')
                    . ' 开始的测试尚未结束.'
                );
                return $dialog->goStage('askContinue');
            }

            return $dialog->goStage('askStart');

        });
    }

    protected function wrapAnswers(array $answers): array
    {
        $answers =  parent::wrapAnswers($answers);
        $answers['0'] = '放弃测试';
        return $answers;
    }

    public function __onRetest(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askConfirm('想要重新测试一次吗?')
            ->wait()
            ->hearing()
            ->isPositive(function(Dialog $dialog){
                $this->score = 0;
                $this->createdAt = new Carbon();
                $this->finish = false;
                $this->finishAt = null;
                $this->questionNumber = 0;
                return $this->next($dialog, 0);
            })
            ->isNegative(function(Dialog $dialog){
                $dialog->say()->info("好的, 测试结束");
                return $dialog->fulfill();
            })->end();
    }

    public function __onAskContinue(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askConfirm('想要继续测试吗?')
            ->wait()
            ->hearing()
            ->isPositive( function(Dialog $dialog){
                return $this->next($dialog, $this->questionNumber);
            })
            ->isNegative(function(Dialog $dialog){
                return $dialog->goStage('retest');
            })->end();
    }

    public function __onAskStart(Stage $stage) : Navigator
    {

        return $stage->buildTalk()
            ->info('您崇尚浪漫的爱情吗？当爱情与婚姻发生冲突的时候，您会怎样选择？爱情与事业中的您是怎样的？一起来做做这套测试题吧，看看您的潜意识中是怎样看待爱情、婚姻与事业的。15秒就可以测出您的性格.')
            ->action(function(Dialog $dialog) {
                return $this->next($dialog, 0 );
            });

    }

    public static function getQuestionDefinition(): array
    {
        return [
'您更喜欢吃那种水果？ ' => [
        'A' => '草莓',
        'B' => '苹果',
        'C' => '西瓜',
        'D' => '菠萝',
        'E' => '橘子',
    ],
'您平时休闲经常去的地方？' => [
        'A' => '郊外',
        'B' => '电影院',
        'C' => '公园',
        'D' => '商场',
        'E' => '酒吧',
        'F' => '练歌房',
    ],
'您认为容易吸引您的人是？' => [
        'A' => '有才气的人',
        'B' => '依赖您的人',
        'C' => '优雅的人',
        'D' => '善良的人',
        'E' => '性情豪放的人',
    ],
'如果您可以成为一种动物，您希望自己是哪种？' => [
        'A' => '猫',
        'B' => '马',
        'C' => '大象',
        'D' => '猴子',
        'E' => '狗',
        'F' => '狮子',
    ],
'天气很热，您更愿意选择什么方式解暑？' => [
        'A' => '游泳',
        'B' => '喝冷饮',
        'C' => '开空调',
    ],
'如果必须与一个您讨厌的动物或昆虫在一起生活，您能容忍哪一个？' => [
        'A' => '蛇',
        'B' => '猪',
        'C' => '老鼠',
        'D' => '苍蝇',
    ],
'您喜欢看哪类电影、电视剧？' => [
        'A' => '悬疑推理类',
        'B' => '童话神话类',
        'C' => '自然科学类',
        'D' => '伦理道德类',
        'E' => '战争枪战类',
    ],
'以下哪个是您身边必带的物品？' => [
        'A' => '打火机',
        'B' => '口红',
        'C' => '记事本',
        'D' => '纸巾',
        'E' => '手机',
    ],
'您出行时喜欢坐什么交通工具？' => [
        'A' => '火车',
        'B' => '自行车',
        'C' => '汽车',
        'D' => '飞机',
        'E' => '步行',
    ],
'以下颜色您更喜欢哪种？' => [
        'A' => '紫',
        'B' => '黑',
        'C' => '蓝',
        'D' => '白',
        'E' => '黄',
        'F' => '红',
    ],
'下列运动中挑选一个您最喜欢的(不一定擅长)？' => [
        'A' => '瑜珈',
        'B' => '自行车',
        'C' => '乒乓球',
        'D' => '拳击',
        'E' => '足球',
        'F' => '蹦极',
    ],
'如果您拥有一座别墅，您认为它应当建立在哪里？' => [
        'A' => '湖边',
        'B' => '草原',
        'C' => '海边',
        'D' => '森林',
        'E' => '城中区',
    ],
'您更喜欢以下哪种天气现象？' => [
        'A' => '雪',
        'B' => '风',
        'C' => '雨',
        'D' => '雾',
        'E' => '雷电',
    ],
'您希望自己的窗口在一座30层大楼的第几层？' => [
        'A' => '七层',
        'B' => '一层',
        'C' => '二十三层',
        'D' => '十八层',
        'E' => '三十层',
    ],
'您认为自己更喜欢在以下哪一个城市中生活？' => [
        'A' => '丽江',
        'B' => '拉萨',
        'C' => '昆明',
        'D' => '西安',
        'E' => '杭州',
        'F' => '北京',
    ]
];

    }

    public function doFinal(Dialog $dialog): ? Navigator
    {
        $this->finishAt = new Carbon();
        $dialog->say([
            'during' => $this->during
        ])->info("测试完毕!用时 %during% 秒. 您的结果是: ");

        $this->sumScore();

        $this->showResult($dialog);

        $dialog->say()->info("退出后, 再次进入可记得结果, 并可重新测试");
        return null;
    }

    protected function showResult(Dialog $dialog) : void
    {
        $say = $dialog->say();
        if ($this->score >= 180) {
            $say->info("意志力强，头脑冷静，有较强的领导欲，事业心强，不达目的不罢休。外表和善，内心自傲，对有利于自己的人际关系比较看重，有时显得性格急噪，咄咄逼人，得理不饶人，不利于自己时顽强抗争，不轻易认输。思维理性，对爱情和婚姻的看法很现实，对金钱的欲望一般。");
            return;
        }

        if ($this->score >= 140) {
            $say->info("聪明，性格活泼，人缘好，善于交朋友，心机较深。事业心强，渴望成功。思维较理性，崇尚爱情，但当爱情与婚姻发生冲突时会选择有利于自己的婚姻。金钱欲望强烈。");
            return;
        }

        if ($this->score >= 100) {
            $say->info("爱幻想，思维较感性，以是否与自己投缘为标准来选择朋友。性格显得较孤傲，有时较急噪，有时优柔寡断。事业心较强，喜欢有创造性的工作，不喜欢按常规办事。性格倔强，言语犀利，不善于妥协。崇尚浪漫的爱情，但想法往往不切合实际。金钱欲望一般。");
            return;
        }

        if ($this->score >= 70) {
            $say->info("好奇心强，喜欢冒险，人缘较好。事业心一般，对待工作，随遇而安，善于妥协。善于发现有趣的事情，但耐心较差，敢于冒险，但有时较胆小。渴望浪漫的爱情，但对婚姻的要求比较现实。不善理财。");
            return;
        }

        if ($this->score >= 40) {
            $say->info("性情温良，重友谊，性格塌实稳重，但有时也比较狡黠。事业心一般，对本职工作能认真对待，但对自己专业以外事物没有太大兴趣，喜欢有规律的工作和生活，不喜欢冒险，家庭观念强，比较善于理财");
            return;
        }

        $say->info("散漫，爱玩，富于幻想。聪明机灵，待人热情，爱交朋友，但对朋友没有严格的选择标准。事业心较差，更善于享受生活，意志力和耐心都较差，我行我素。有较好的异性缘，但对爱情不够坚持认真，容易妥协。没有财产观念。");
    }

    protected function sumScore() : void
    {
        $scoreDef = [
[
    'A' => 2,
    'B' => 3,
    'C' => 5,
    'D' => 10,
    'E' => 15,
],
[
    'A' => 2,
    'B' => 3,
    'C' => 5,
    'D' => 10,
    'E' => 15,
    'F' => 20,
],
[
    'A' => 2,
    'B' => 3,
    'C' => 5,
    'D' => 10,
    'E' => 15,
],
[
    'A' => 2,
    'B' => 3,
    'C' => 5,
    'D' => 10,
    'E' => 15,
    'F' => 20,
],
[
    'A' => 5,
    'B' => 10,
    'C' => 15,
],
[
    'A' => 2,
    'B' => 5,
    'C' => 10,
    'D' => 15,
],
[
    'A' => 2,
    'B' => 3,
    'C' => 5,
    'D' => 10,
    'E' => 15,
],
[
    'A' => 2,
    'B' => 2,
    'C' => 3,
    'D' => 5,
    'E' => 10,
],
[
    'A' => 2,
    'B' => 3,
    'C' => 5,
    'D' => 10,
    'E' => 15,
],
[
    'A' => 2,
    'B' => 3,
    'C' => 5,
    'D' => 8,
    'E' => 12,
    'F' => 15,
],
[
    'A' => 2,
    'B' => 3,
    'C' => 5,
    'D' => 8,
    'E' => 1,
    'F' => 15,
],
[
    'A' => 2,
    'B' => 3,
    'C' => 5,
    'D' => 10,
    'E' => 15,
],
[
    'A' => 2,
    'B' => 3,
    'C' => 5,
    'D' => 10,
    'E' => 15,
],
[
    'A' => 2,
'B' => 3,
'C' => 5,
'D' => 10,
'E' => 15,
],
[
    'A' => 1,
    'B' => 3,
    'C' => 5,
    'D' => 8,
    'E' => 10,
    'F' => 15,
]
    ];

        $score = 0;

        foreach ($this->answers as $q => $a) {
            if (isset($scoreDef[$q][$a])) {
                $score += $scoreDef[$q][$a];
            }
        }
        $this->score = $score;
    }

    public function __hearing(Hearing $hearing) : void
    {
        $hearing->is('quit', function(Dialog $dialog){
            $dialog->say()->info("好的, 测试退出. 下次可以继续当前的进度(基于上下文记忆)");
            return $dialog->cancel();
        });
    }

    protected function defaultFallback(): ? callable
    {
        return function(Dialog $dialog){
            $dialog->say()
                ->warning("不好意思无法理解您的意思. 请输入答案的序号, 或者输入'quit'退出测试");

            return $dialog->repeat();
        };
    }

    protected function onAnswered(Dialog $dialog, int $questionIndex, $choice): ? Navigator
    {
        if ($choice == '0') {
            return $dialog->goStage('cancel');
        }
        return null;
    }

    public function __onCancel(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->info('将要退出测试, 保留进度, 下次返回继续 (多轮对话功能点)')
            ->action(function (Dialog $dialog) {
                return $dialog->cancel();
            });

    }

    public function __getDuring() : string
    {
        $finishAt = $this->finishAt;
        $createdAt = $this->createdAt;

        $end = $finishAt ? $finishAt->timestamp : 0;
        $start = $createdAt ? $createdAt->timestamp : 0;

        if (empty($start) || empty($end)) {
            return '??';
        }

        $gap = $end - $start;
        $gap = $gap > 0 ? $gap : 0;
        return (string) $gap;
    }

}