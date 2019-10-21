<?php


namespace Commune\Chatbot\OOHost\Context\Intent;


use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Contracts\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Registrar\AbsContextRegistrar;

/**
 * @method  IntentDefinition|null getDef(string $contextName) : ? Definition
 */
class IntentRegistrarDefault extends AbsContextRegistrar implements IntentRegistrar
{
    const DEF_CLAZZ = IntentDefinition::class;

    /**
     * 匹配出最有可能存在的intent
     * 通常在 Session 内部使用.
     *
     * @param NLU $nlu
     * @param Message $message
     * @return IntentMessage|null
     */
    public function matchIntent(NLU $nlu, Message $message) : ? IntentMessage
    {
        // 检查 message 本身是否就是 intent
        if ($message instanceof IntentMessage) {
            return $message;
        }

        // matched 是排他的
        $matched = $nlu->getMatchedIntent();

        if (isset($matched)) {
            $names = [$matched]; // 排他
        } else {
            $names = $nlu->getPossibleIntentNames(true);
        }

        if (empty($names)) {
            return null;
        }

        // 按优先级顺序进行遍历. 第一个有def的就是匹配到的.
        foreach ($names as $name) {
            if ($this->hasDef($name)) {
                return $this->getDef($name)->newContext(
                    $nlu->getIntentEntities($name)->all()
                );
            }
        }

        return null;
    }

    /**
     * 按intent name 进行匹配.
     * 不考虑是否已经存在 matchedIntent
     *
     * @param string $intentName
     * @param NLU $nlu
     * @param Message $message
     * @return IntentMessage|null
     */
    public function matchCertainIntent(
        string $intentName,
        NLU $nlu,
        Message $message
    ) : ? IntentMessage
    {

        if (! $this->hasDef($intentName)) {
            return $this->shouldUsePlaceholder($intentName, $nlu);
        }

        // 使用 Definition 进行主动匹配. 如果定义了主动匹配的逻辑.
        $expectDef = $this->getDef($intentName);

        if ($nlu->hasPossibleIntent($intentName)) {
            $entities = $nlu->getIntentEntities($intentName)->all();
            return $expectDef->newContext($entities);
        }

        return $this->doMatch($message, $expectDef);

    }

    /**
     * 用 placeholder 处理没有预定义的意图.
     *
     * @param string $intentName
     * @param NLU $nlu
     * @return IntentMessage|null
     */
    protected function shouldUsePlaceholder(string $intentName, NLU $nlu) : ? IntentMessage
    {
        if (!$nlu->hasPossibleIntent($intentName)) {
            return null;
        }
        $entities = $nlu->getIntentEntities($intentName);
        return new PlaceHolderIntent($intentName, $entities->all());
    }


    /**
     * 主动匹配逻辑.
     *
     * @param Message $origin
     * @param IntentDefinition $expectDef
     * @return IntentMessage|null
     */
    protected function doMatch(Message $origin, IntentDefinition $expectDef): ? IntentMessage
    {
        // 使用matcher
        $entities = $expectDef->getMatcher()->match($origin);
        if (isset($entities)) {
            $matched = $expectDef->newContext($entities);
            return $matched;
        }

        return null;
    }

    public function isCommandIntent(string $intentName) : bool
    {
        if (!$this->hasDef($intentName)) {
            return false;
        }
        $matcher = $this->getDef($intentName)->getMatcher();
        return $matcher->hasCommand();
    }


}