<?php


namespace Commune\Chatbot\OOHost\NLU\Contracts;


use Commune\Chatbot\OOHost\Context\Intent\IntentDefinition;
use Commune\Chatbot\OOHost\NLU\Options\IntentCorpusOption;

/**
 * 表示 intentDefinition 上有默认的 corpus 信息.
 * 可以在 corpus 读取 intentCorpus 时自动合并相关信息.
 * 只有 corpus 没有存储本地信息时, 才会使用该信息.
 *
 * 拆分这个 interface, 就不会让 intent 模块倒过来耦合 corpus 模块.
 */
interface IntentDefHasCorpus extends IntentDefinition
{
    public function getDefaultCorpus() : IntentCorpusOption;

}