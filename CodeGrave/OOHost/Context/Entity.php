<?php


namespace Commune\Chatbot\OOHost\Context;

use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * Entity 是 context 定义的属性.
 * 相当于一个 php class 的 construct方法定义的参数.
 * 这些参数必须传入Context 之后, context 才能真正运行 ( __onStart )
 *
 * 只不过这些参数需要通过多轮对话来获取, 而且可以记忆.
 * 每一个 entity 都为多轮对话定义一个环节 (stage).
 * 可以通过在 context 里定义 __on{$name} 方法, 来重定义该entity的stage
 *
 * @property-read string $name
 * @property-read string $question
 */
interface Entity
{
    const STAGE_METHOD = 'asStage';


    /**
     * 对该 context 的  entity 进行赋值.
     * @param Context $self
     * @param mixed $value
     */
    public function set(Context $self, $value) : void;

    /**
     * 从 context 中获取一个entity 的值.
     * @param Context $self
     * @return mixed
     */
    public function get(Context $self);

    /**
     * 确认 context 是否包含一个完整正确的 entity 的值.
     *
     * @param Context $self
     * @return bool
     */
    public function isPrepared(Context $self) : bool;

    /**
     * 定义多轮对话的环节.
     * @param Stage $stageRoute
     * @return Navigator
     */
    public function asStage(Stage $stageRoute) : Navigator;

}