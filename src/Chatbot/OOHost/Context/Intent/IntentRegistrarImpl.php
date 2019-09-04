<?php


namespace Commune\Chatbot\OOHost\Context\Intent;


use Commune\Chatbot\OOHost\Context\ContextRegistrarImpl;
use Commune\Chatbot\OOHost\Session\Session;

/**
 * @method  IntentDefinition|null getDef(string $contextName) : ? Definition
 */
class IntentRegistrarImpl extends ContextRegistrarImpl implements IntentRegistrar
{
    const DEF_CLAZZ = IntentDefinition::class;

    /**
     * 根据有可能存在的intent, 进行匹配.
     * @param Session $session
     * @return IntentMessage|null
     */
    public function matchIntent(Session $session) : ? IntentMessage
    {
        $matched = $session->getMatchedIntent();
        if (isset($matched)) {
            return $matched;
        }

        $nlu = $session->nlu;

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

        $first = $names[0];
        $intent = null;
        // 按优先级顺序进行遍历.
        foreach ($names as $name) {
            if ($this->hasDef($name)) {
                $intent = $this->getDef($name)->newContext(
                    $nlu->getIntentEntities($name)
                );
                break;
            }
        }

        // 如果都没有注册的, 则用个占位符.
        return $intent ?? new PlaceHolderIntent(
            $first,
            $nlu->getIntentEntities($first)
        );
    }

    /**
     * 按intent name 进行匹配.
     * 不考虑是否已经存在 matchedIntent
     *
     * @param string $intentName
     * @param Session $session
     * @return IntentMessage|null
     */
    public function matchCertainIntent(string $intentName, Session $session) : ? IntentMessage
    {
        if (! $this->hasDef($intentName)) {
            return null;
        }

        // 必须用definition, 因为intentName 可能有很多种...
        // 牺牲一点性能获取工程上的便利.
        // 但有可能造成歧义. 需要继续权衡.
        $def = $this->getDef($intentName);
        $matcher = $def->getMatcher();
        $intent = $this->doMatch($session, $def, $matcher);

        // 会主动设置到 session 中.
        if (isset($intent)) {
            $intent = $intent->toInstance($session);
            $session->setMatchedIntent($intent);
        }

        return $intent;
    }



    protected function doMatch(Session $session, IntentDefinition $def, IntentMatcher $matcher): ? IntentMessage
    {
        $name = $def->getName();
        // 检查 matched
        $matched = $session->getMatchedIntent();
        if (isset($matched) && $matched->getName() === $name) {
            return $matched;
        }

        // 检查incoming message
        $nlu = $session->nlu;

        // 必须高于阈值的意图才会被识别.
        // 是否已经包含.
        if ($nlu->hasPossibleIntent($name, true)) {
            $entities = $nlu->getIntentEntities($name);
            return $def->newContext($entities);
        }

        // 使用matcher
        $origin = $session->incomingMessage->message;
        $entities = $matcher->match($origin);
        if (isset($entities)) {
            return $def->newContext($entities);
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