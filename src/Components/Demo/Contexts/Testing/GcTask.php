<?php


namespace Commune\Components\Demo\Contexts\Testing;


use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Components\Demo\Contexts\DemoHome;

/**
 * @property-read Context $depend
 */
class GcTask extends TaskDef
{

    public function __construct(Context $depend)
    {
        parent::__construct(get_defined_vars());
    }

    public static function __depend(Depending $depending): void
    {
    }

    public function __onStart(Stage $stage): Navigator
    {
        $selfC = $this->_gc_count(); //expect 0;
        $depend = $this->depend;
        $dependC = $this->depend->_gc_count(); //expect 1;

        $info1 = "第一步, 生成GCTask, 自己的 c 是 $selfC (预期0 ), 依赖对象的C 是$dependC (预期1)";


        $this->depend = new DemoHome();
        $selfC = $this->_gc_count();
        $newDependC = $this->depend->_gc_count(); //expect 1;
        $oldDependC = $depend->_gc_count();

        $info2 = "第二步, gctask 赋值新的依赖对象, 自己的 c 是 $selfC (0), 新依赖的c为$newDependC(1), 老依赖的c 为$oldDependC (0)";

        return $stage->buildTalk()
            ->info($info1)
            ->info($info2)
            ->fulfill();
    }

    public function __exiting(Exiting $listener): void
    {
    }


}