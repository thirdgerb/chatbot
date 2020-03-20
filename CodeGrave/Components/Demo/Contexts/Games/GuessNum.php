<?php


namespace Commune\Studio\Components\Demo\Guest;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * @property int $lt  大边界(含)
 * @property int $gt  小边界(含)
 * @property int $turn 次数
 */
class GuessNum extends TaskDef
{
    const DESCRIPTION = '二分法猜数字小游戏';

    const GT = 1;
    const LT = 1000;

    public function __construct(array $props = [])
    {
        parent::__construct([
            'lt' => self::LT,
            'gt' => self::GT,
            'turn' => 0,
        ]);
    }

    public function __hearing(Hearing $hearing) : void
    {
        $hearing->is('b', [Redirector::class, 'cancel']);
    }

    public function __onStart(Stage $stage): Navigator
    {

        return $stage->buildTalk()
            ->info(<<<EOF
欢迎来到猜数字小游戏 (用于测试多轮对话) 

请您在心中设想一个1 到 1000之间的整数, 我来猜它是什么.

随时输入 'b' 退出测试 (不会记录).
EOF
            )
            ->askConfirm('开始吗?')
            ->wait()
            ->hearing()
            ->isPositive(function(Dialog $dialog){
                $bool = rand(0, 1) > 0;
                return $dialog->goStage($bool ? 'askGt' : 'askLt');
            })
            ->isNegative(function(Dialog $dialog){
                $dialog->say()->info('好的, 测试退出');
                return $dialog->cancel();
            })
            ->end();
    }

    protected function fallback(Dialog $dialog) : Navigator
    {
        $dialog
            ->say()
            ->info("无法理解您的意思.输入y表示yes, n表示no. 也可以试着输入'是的', '不是'. 输入'b' 随时退出 ");

        return $dialog->repeat();
    }


    protected function shouldGuess() : bool
    {
        return $this->lt - $this->gt < 3;
    }

    protected function middle() : int
    {
        return (int) ceil(($this->lt + $this->gt) / 2);
    }

    /**
     * 确认右边界
     * @param Stage $stage
     * @return Navigator
     */
    public function __onAskGt(Stage $stage) : Navigator
    {
        if ($this->shouldGuess()) {
            return $stage->dialog->goStage('guess');
        }

        $middle = $this->middle();

        return $stage->buildTalk()
            ->withSlots([
                'lt' => $this->lt,
                'gt' => $this->gt,
                'md' => $middle
            ])
            ->askConfirm("您想的数字比 %md% 【大】 吗? ")
            ->wait()
            ->hearing()
            ->isPositive(function(Dialog $dialog) use ($middle){
                $this->gt = $middle + 1;
                $this->turn = $this->turn + 1;
                return $dialog->goStage('askGt');
            })
            ->isNegative(function(Dialog $dialog) use ($middle) {
                $this->lt = $middle;
                $this->turn = $this->turn + 1;
                return $dialog->goStage('askGt');
            })
            ->end();

    }


    /**
     * 确认左边界
     * @param Stage $stage
     * @return Navigator
     */
    public function __onAskLt(Stage $stage) : Navigator
    {
        if ($this->shouldGuess()) {
            return $stage->dialog->goStage('guess');
        }

        $middle = $this->middle();

        return $stage->buildTalk()
            ->withSlots([
                'lt' => $this->lt,
                'gt' => $this->gt,
                'md' => $middle
            ])
            ->askConfirm("您想的数字比 %md% 【小】 吗? ")
            ->wait()
            ->hearing()
            ->isPositive(function(Dialog $dialog) use ($middle){
                $this->lt = $middle - 1;
                $this->turn = $this->turn + 1;
                return $dialog->goStage('askLt');
            })
            ->isNegative(function(Dialog $dialog) use ($middle) {
                $this->gt = $middle;
                $this->turn = $this->turn + 1;
                return $dialog->goStage('askLt');
            })
            ->end();
    }

    public function __onGuess(Stage $stage) : Navigator
    {
        $lt = $this->lt;
        return $stage->buildTalk()
            ->askConfirm("您想的数字是 $lt 吗?")
            ->wait()
            ->hearing()
            ->isPositive(function(Dialog $dialog){
                $dialog
                    ->say(['turn' => $this->turn])
                    ->info("感谢配合! 共用 %turn%轮. 测试结束");
                return $dialog->fulfill();
            })
            ->isNegative(function(Dialog $dialog){

                if ($this->gt == $this->lt) {
                    $dialog->say()->error("不可能, 逗我玩吧? 测试取消");
                    return $dialog->cancel();
                }
                $this->lt = $this->lt - 1;
                $this->turn =  $this->turn + 1;
                return $dialog->repeat();
            })
            ->end();

    }


    public static function __depend(Depending $depending): void
    {
    }

    public function __exiting(Exiting $listener): void
    {
    }


}