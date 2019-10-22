<?php


namespace Commune\Components\Demo\Cases\Drink;


use Carbon\Carbon;
use Commune\Chatbot\App\Callables\Predictions\IsNumeric;
use Commune\Chatbot\App\Intents\ActionIntent;
use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\NLU\Contracts\Corpus;
use Commune\Chatbot\OOHost\NLU\Options\EntityDictOption;
use Commune\Components\Demo\Cases\Drink\Memories\OrderMem;
use Commune\Support\Utils\StringUtils;
use Commune\Components\Predefined\Intents\Attitudes;
use Commune\Components\Predefined\Intents\Dialogue\RandomInt;

/**
 * 购买果汁的测试用例. 新版开发了大概三个小时.
 *
 * 测试点包括:
 *
 * - 意图正确的流程执行
 * - 实体匹配, nlu 匹配
 * - 各种中断和重试逻辑能否响应
 * - 各种插入的闲聊逻辑能否响应
 * - 单步参数校验.
 *
 *
 * @property string $juice_fruit 果汁类型. 同名 entity
 * @property string $juice_ice 是否加冰. 值为 "juice_ice" 或 "juice_no_ice"
 * @property string $juice_pack 是否打包. 值为 "juice_cup" 或 "juice_bowl"
 * @property bool|null $isSetByMemory 当前是否是假设的订单.
 * @property bool|null $forFree 是否免单.
 *
 * @property bool $isPaying 是否开始付账了.
 * @property int $needToPay 需要支付的金额
 * @property int $isPaid 已付的钱数
 *
 * @property int $askPaidTime
 */
class OrderJuiceInt extends ActionIntent
{
    const DESCRIPTION = '购买果汁';

    // 命令名. 可以用命令的方式来匹配
    const SIGNATURE = 'orderJuice
    {juice_fruit : 请问需要什么口味的果汁}
    {juice_ice : 请问是否加冰}
    {juice_pack : 请问是碗装还是杯装}
    ';
    
    const FRUIT = 'juice_fruit';
    const ICE = 'juice_ice';
    const PACK = 'juice_pack';

    const CASTS = [
        self::FRUIT => 'string',
        self::ICE => 'string',
        self::PACK => 'string'
    ];

    /**
     * @var OrderMem $orderMem
     */
    protected $orderMem;

    public static function getContextName(): string
    {
        return StringUtils::normalizeContextName('demo.cases.orderjuice');
    }

    public static function __depend(Depending $depending): void
    {
        $depending
            ->on(self::FRUIT)
            ->on(self::ICE)
            ->on(self::PACK);
    }

    public function __exiting(Exiting $listener): void
    {
        $listener
            ->onCancel(function(Dialog $dialog) {
                $this->falwell($dialog);
                return $dialog->cancel(true);
            })

            ->onQuit(function(Dialog $dialog) {
                $this->falwell($dialog);
                return $dialog->quit(true);
            });
    }

    public function navigate(Dialog $dialog): ? Navigator
    {
        $this->toInstance($dialog->session);
        $this->welcome($dialog);
        return parent::navigate($dialog);
    }

    public function falwell(Dialog $dialog): void
    {
        $paid = $this->isPaid ?? 0;

        if ($paid > 0) {
            $dialog->say()->info("好的, 这是您支付的钱 $paid 元, 还给您请收好");
        }

        $dialog->say()->info('再见! 欢迎下次再来');
    }

    public function welcome(Dialog $dialog): void
    {
        $order = OrderMem::fromSession($dialog->session);
        // 欢迎语
        $order->times = $times = $order->times + 1;

        if ($times === 1) {
            $dialog->say()->info("顾客您好! 欢迎您第一次来到本店!");
        } else {
            $dialog->say()->info("顾客您好! 这是您第{$times}次来到本店, 感谢惠顾!");
        }

        // 订单确认.
        $fruit = $order->lastFruit;
        if (empty($fruit)) {
            return;
        }

        $this->juice_fruit = $order->lastFruit;
        $this->juice_ice = $order->lastIce;
        $this->juice_pack = $order->lastPack;
        $this->isSetByMemory = true;
    }

    public function __hearing(Hearing $hearing): void
    {
        // 注册正常命中不了之后执行的逻辑.
        $hearing->component([$this, 'afterHearing']);
    }

    /**
     * 帮助文档.
     * @param Dialog $dialog
     * @return Navigator
     */
    public function __help(Dialog $dialog) : Navigator
    {
        $dialog->say()
            ->info("不好意思, 作为测试的Demo, 我也不知道能怎么帮您. 如果流程卡主了请尝试输入 #cancel . 最多再免个单吧..");

        $this->forFree = true;

        return $dialog->repeat();
    }


    public function __onJuice_fruit(Stage $stage) : Navigator
    {

        /**
         * @var Corpus $corpus
         * @var EntityDictOption $option
         */
        $corpus = $stage->dialog->app->make(Corpus::class);
        $option = $corpus->entityDictManager()->get(self::FRUIT);


        $builder =  $stage->buildTalk()
            ->askChoose(
                '我们这儿有以下各种果汁, 样样15元, 15元一样, 请挑选您喜欢的口味. (由于是临时开发的 Demo, 暂不支持购买多个)',
                $option->values
            )
            ->hearing();

        foreach ($option->values as $index => $value) {

            $builder->isChoice(
                $index,
                function(Dialog $dialog) use ($value) : Navigator {
                    $this->juice_fruit = $value;

                    $dialog->say()->info("好的, 为您选择了 $value 口味");
                    return $dialog->next();
                }
            );
        }

        return $builder->end(function(Dialog $dialog) {
            $dialog->say()->warning("不好意思, 我们没有别的水果");

            return $dialog->repeat();
        });
    }

    public function __onJuice_ice(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askConfirm(
                '请问[需要](y)[加冰](y)吗?',
                true
            )
            ->hearing()
            ->isPositive(function(Dialog $dialog) {
                $this->juice_ice = self::ICE;
                $dialog->say()->info("好的, 给您加冰.");
                return $dialog->next();
            })
            ->isNegative(function(Dialog $dialog) {
                $this->juice_ice = 'juice_no_ice';
                $dialog->say()->info("好的, 给您常温的.");
                return $dialog->next();
            })
            ->end();
    }

    public function __onJuice_pack(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askChoose(
                '请问是杯装还是碗装',
                [
                    '杯装',
                    '盒装',
                ],
                0
            )
            ->hearing()
            ->isChoice(0, function(Dialog $dialog) {
                $this->juice_pack = 'juice_cup';
                $dialog->say()->info("好的, 给您用杯装");
                return $dialog->next();
            })
            ->isChoice(1, function(Dialog $dialog) {
                $this->juice_pack = 'juice_bowl';
                $dialog->say()->info("好的, 给您用碗装");
                return $dialog->next();
            })
            ->end(function(Dialog $dialog) {
                $dialog->say()->warning("不好意思, 我们只有杯和碗两种包装");
                return $dialog->repeat();
            });

    }


    /**
     * 确认订单环节.
     *
     * @param Stage $stageRoute
     * @return Navigator
     */
    public function action(Stage $stageRoute): Navigator
    {
        $order = $this->toOrderStr();

        if ($this->isSetByMemory === true) {

            return $stageRoute->buildTalk()
                ->askConfirm(
                    "您上次的订单是 $order, 这次还和上次一样吗? ",
                    true
                )
                ->hearing()
                ->isPositive([$this, 'goPay'])
                ->isNegative(function(Dialog $dialog) {
                    $this->clearOrder();
                    return $dialog->restart();
                })
                ->end();
        }

        return $stageRoute
            ->buildTalk()
            ->askConfirm(
                "您的订单是 $order; 确认吗? "
            )
            ->hearing()
            ->isPositive([$this, 'goPay'])
            ->isNegative(function(Dialog $dialog) : Navigator {
                $dialog->say()->info('不好意思, 麻烦重新选择订单');
                $this->clearOrder();
                return $dialog->restart();
            })
            ->end();

    }

    public function goPay(Dialog $dialog)
    {
        $this->isPaying = true;
        $this->needToPay = $this->forFree === true ? 0 : 15;
        return $dialog->goStage('pay');
    }

    /**
     * 付款环节.
     *
     * @param Stage $stage
     * @return Navigator
     */
    public function __onPay(Stage $stage) : Navigator
    {
        $casts = $this->needToPay;
        if ($casts === 0) {
            return $stage->dialog->goStage('final');
        }

        return $stage
            ->buildTalk()
            ->askVerbose(
                "请您支付 $casts 元, 谢谢! (请用纯数字模拟支付)",
                [ 15, 0, -15, 20, 10.5]
            )
            ->hearing()
            ->expect(
                new IsNumeric(),
                function (Dialog $dialog, VerboseMsg $message) {
                    $paid = floatval($message->getTrimmedText());
                    $paid = round($paid, 2);

                    // 瞎搞
                    if ($paid < 0) {
                        $dialog->say()->info("咋地啦, 要我倒找钱给您哈?");

                    // 不付钱
                    } elseif ($paid == 0) {
                        $times = $this->askPaidTime ?? 0;
                        $this->askPaidTime = $times + 1;

                        $dialog->say()->info("呃.... ");

                    // 找钱
                    } elseif ($paid > $this->needToPay) {

                        $this->isPaid = $this->needToPay;
                        $change = $paid - $this->needToPay;
                        $this->needToPay = 0;

                        $dialog->say([
                            'paid' => $paid,
                            'change' => $change
                        ])->info('您支付了 %paid% 元, 找您 %change% 元');
                        return $dialog->goStage('final');

                    // 正好
                    } elseif ($paid == $this->needToPay) {

                        $this->isPaid = $this->isPaid + $paid;
                        $this->needToPay = 0;
                        $dialog->say(['paid' => $paid])
                            ->info('您支付了 %paid% 元, 刚好, 真棒!');

                    // 不够
                    } elseif ($paid < $this->needToPay) {
                        $this->isPaid = $this->isPaid + $paid;
                        $this->needToPay = $this->needToPay - $paid;

                        $dialog->say(['paid' => $paid, 'need' => $this->needToPay])
                            ->info('您支付了 %paid% 元, 还需要 %need%元');

                    }

                    return $dialog->repeat();
                }
            )
            ->runDefaultFallback()
            ->end(function(Dialog $dialog) {

                $times = $this->askPaidTime ?? 0;
                $this->askPaidTime = $times + 1;

                if ($this->askPaidTime <= 3) {
                    $dialog->say()->info("不好意思, 没有明白您的意思~");
                    return $dialog->repeat();
                }

                if ($this->askPaidTime <= 5) {
                    $dialog->say()->warning("干嘛呢, 拿钱啊, 瞧不起机器人是吧!");
                    return $dialog->repeat();
                }

                $dialog->say()->error("气炸了!系统错误!系统错误!");
                return $dialog->cancel(true);
            });
    }

    /**
     * 结束
     * @param Stage $stage
     * @return Navigator
     */
    public function __onFinal(Stage $stage) : Navigator
    {
        return $stage->onStart(function(Dialog $dialog) {
            $mem = OrderMem::from($this);
            $mem->lastPack = $this->juice_pack;
            $mem->lastFruit = $this->juice_fruit;
            $mem->lastIce = $this->juice_ice;

            // 完成收银.
            $this->clearPaid();

            $order = $this->toOrderStr();
            $deliverAt = new Carbon();
            $deliverAt->addSeconds(60);

            $deliver = (new Text("来自果汁店: 您好, 这是您点的 $order , 再次感谢惠顾"))->deliverAt($deliverAt);

            $dialog
                ->say()
                ->info("谢谢, 您的订单正在制作中, 完成后立刻给您送去 (试图发送消息) ")
                ->info($deliver);

            return null;
        })
        ->buildTalk()
        ->askConfirm("请问还有别的需求吗?")
        ->hearing()
        ->isPositive(function(Dialog $dialog) {
            $this->clearOrder();
            return $dialog->restart();
        })
        ->isNegative(function(Dialog $dialog){
            $this->falwell($dialog);
            return $dialog->fulfill();
        })
        // 太麻烦... 直接结束吧
        ->end(function(Dialog $dialog){
            $this->falwell($dialog);
            return $dialog->fulfill();
        });
    }




    /*---------- hearing 注册内容 -----------*/

    /**
     * 在 hearing 结束的时候才执行, 不会喧宾夺主.
     * @param Hearing $hearing
     */
    public function afterHearing(Hearing $hearing): void
    {
        $hearing
            ->runIntentIn(['navigation.'])
            // 打招呼
            ->isIntent(Attitudes\GreetInt::class, [$this, 'greetBack'])
            // 被表扬了, 免单!
            ->isIntent(Attitudes\ComplementInt::class, [$this, 'thanksBack'])
            // 被骂了, 何苦呢
            ->isIntent(Attitudes\DissInt::class, [$this, 'apology'])
            // 如果发起了另一个订单.
            // 相当于重新点单
            ->isIntent(static::getContextName(), [$this, 'changeOrder'])
            // 要求随便
            ->isIntent(RandomInt::class, [$this, 'randomChoose'])
            ->isIntent(Attitudes\ThanksInt::class, [$this, 'thanksBack'])
            ->todo([$this, 'matchEntities']) 
                ->matchEntity(self::FRUIT)
                ->matchEntity(self::ICE)
                ->matchEntity(self::PACK)
            ->otherwise();
    }

    protected function clearOrder()
    {
        $this->juice_fruit = null;
        $this->juice_pack = null;
        $this->juice_ice = null;
    }

    protected function clearPaid()
    {
        $this->isPaying = null;
        $this->isPaid = null;
        $this->needToPay = null;
    }




    /*--------- 各种反应逻辑 -------*/

    public function greetBack(Dialog $dialog) : ? Navigator
    {
        $dialog->say()->info("您好呀! 欢迎来到我们的店. ");
        return null;
    }


    public function changeOrder(OrderJuiceInt $intent, Dialog $dialog) : ? Navigator
    {
        if (isset($intent->juice_fruit)) {
            $this->juice_fruit = $intent->juice_fruit;
        }

        if (isset($intent->juice_ice)) {
            $this->juice_ice = $intent->juice_ice;
        }

        if (isset($this->juice_pack)) {
            $this->juice_pack = $intent->juice_pack;
        }

        return $dialog->restart();
    }

    public function thanksBack(Dialog $dialog) : ? Navigator
    {
        $dialog->say()->info("不用谢! 感谢您的惠顾才对!");
        return $dialog->rewind();
    }

    public function randomChoose(Dialog $dialog, Corpus $corpus) : ? Navigator
    {
        if ($this->isPaying) {
            $dialog->say()->info("这种事情怎么能随便呢!!");
            return $dialog->rewind();
        }


        $dialog->say()->info("好的, 我就替您做选择了");

        /**
         * @var string[] $fruits
         * @var string[] $ice
         * @var string[] $pack
         */
        $manager = $corpus->entityDictManager();
        $fruits = array_values($manager->get(self::FRUIT)->values);
        $ice = array_values($manager->get(self::ICE)->values);
        $pack = array_values($manager->get(self::PACK)->values);

        $this->juice_fruit = $fruits[array_rand($fruits)];
        $this->juice_ice = $ice[array_rand($ice)];
        $this->juice_pack = $pack[array_rand($pack)];

        return $dialog->restart();
    }

    public function apology(Dialog $dialog) : ? Navigator
    {
        $dialog->say()->warning("如果工作上有什么失误请原谅! 不过美国 siri 纵容羞辱言论最近被人批判了, 所以...");
        return $dialog->rewind();
    }

    public function matchEntities(Dialog $dialog, NLU $nlu) : ? Navigator
    {
        $matched = $nlu->getMatchedEntities();
        $fruit = $matched->get(self::FRUIT);
        $pack = $matched->get(self::PACK);
        $ice = $matched->get(self::ICE);

        $re = '';
        if (!empty($fruit)) {
            $this->juice_fruit = $fruit;
            $re = true;
        }
        if (!empty($pack)) {
            $this->juice_pack = $pack;
            $re = true;
        }
        if (!empty($ice)) {

            // 考虑到实体匹配搞不好两种都匹配了, 是否的实体匹配太难做
            if (is_array($ice) && in_array('juice_no_ice', $ice)) {
                $this->juice_ice = 'juice_no_ice';

            } else {
                $this->juice_ice = $ice;
            }

            $re = true;
        }

        if ($re) {
            return $dialog->restart();
        }

        return null;
    }

    public function toOrderStr() : string
    {
        $fruit = $this->juice_fruit;
        $ice = $this->juice_ice === self::ICE ? '加冰' : '不加冰';
        $pack = $this->juice_pack === 'juice_cup' ? '杯装' : '碗装';

        if (empty($fruit)) {
            return '';
        }

        return "{$fruit}口味的果汁, $ice, $pack";
    }
}