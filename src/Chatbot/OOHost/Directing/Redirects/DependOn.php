<?php


namespace Commune\Chatbot\OOHost\Directing\Redirects;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Directing\Stage\GoStagePipes;

/**
 * 当前context 依赖一个目标 context
 * 目标context fulfill 的话, 会作为参数回调
 * 如果目标context 发生异常 cancel, reject, fail 的话, 当前context 也会因此退出.
 */
class DependOn extends Redirector
{
    /**
     * @var array
     */
    protected $stages;

    public function __construct(
        Dialog $dialog,
        Context $to,
        array $stages = []
    )
    {
        $this->stages = $stages;
        parent::__construct($dialog, $to);
    }

    public function doDisplay(): ? Navigator
    {
        $this->history->dependOn($this->to);
        if (empty($this->stages)) {
            return $this->startCurrent();
        }

        return new GoStagePipes(
            $this->dialog,
            $this->stages,
            true
        );
    }


}