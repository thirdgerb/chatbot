<?php


namespace Commune\Chatbot\OOHost\Context\Intent;

use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 对用户意图的描述. 也被定义为context
 *
 * 参考 DuerOS 添加两个property:
 *
 * @property bool|null $isConfirmed
 * @property bool[] $confirmedEntities
 *
 * 这是因为在语音OS 等场景下, 意图匹配的结果不一定正确, 需要逐个校验.
 * 校验完就应该有个地方来设置.
 *
 * 理论上所有的来自匹配获得的 entity 都应该是数组.
 */
interface IntentMessage extends Context
{
    // intent 自身是否已确认
    const INTENT_CONFIRMATION = 'isConfirmed';

    // intent 的属性是否已确认.
    const ENTITIES_CONFIRMATION = 'confirmedEntities';

    /**
     * 当一个Intent 试图拦截当前的对话时, 会执行此方法.
     * 通常是 return $dialog->sleepTo($this);
     * 从而进入intent 自己的逻辑.
     *
     * @param Dialog $dialog
     * @return Navigator|null
     */
    public function navigate(Dialog $dialog) : ? Navigator;

}