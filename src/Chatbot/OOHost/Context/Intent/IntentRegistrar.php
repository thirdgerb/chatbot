<?php


namespace Commune\Chatbot\OOHost\Context\Intent;


use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Session\Session;

/**
 * @method  IntentDefinition|null get(string $contextName) : ? Definition
 */
class IntentRegistrar extends ContextRegistrar
{
    const DEF_CLAZZ = IntentDefinition::class;

    /**
     * @var IntentMatcher[]
     */
    protected $matchers = [];

    /**
     * @param IntentDefinition $def
     * @param bool $force
     * @return bool
     */
    public function register(Definition $def, bool $force = false): bool
    {
        $register = parent::register($def, $force);

        if (false === $register) {
            return false;
        }
        // 注册 matcher
        $option = $def->getMatcherOption();
        $this->registerMatcher($def->getName(), $option);
        ContextRegistrar::getIns()->register($def, $force);
        return true;
    }

    public function registerMatcher(
        string $intentName,
        IntentMatcherOption $option
    ) : void
    {
        $intentName = $this->filterContextName($intentName);

        if (isset($this->matchers[$intentName])) {
            $matcher = $this->matchers[$intentName];

        } else {
            $matcher = new IntentMatcher($intentName);
            $this->matchers[$intentName] = $matcher;

        }
        $matcher->mergeOption($option);
    }

    public function getMatcher(string $intentName) : ?  IntentMatcher
    {
        $intentName = $this->filterFetchId($intentName);
        return $this->matchers[$intentName] ?? null;
    }

    public function matchAny(Session $session) : ? IntentMessage
    {
        $matched = $session->getMatchedIntent();
        if (isset($matched)) {
            return $matched;
        }

        $incomingMessage = $session->incomingMessage;
        $name = $incomingMessage->getHighlyPossibleIntent();
        if (empty($name)) {
            return null;
        }

        if ($this->has($name)) {
            return $this->get($name)->newContext(
                $incomingMessage->getPossibleIntentEntities($name)
            );
        }

        return null;
    }

    public function matchIntent(string $intentName, Session $session) : ? IntentMessage
    {
        if (! $this->has($intentName)) {
            return null;
        }

        $def = $this->get($intentName);
        $matcher = $this->getMatcher($intentName);
        $intent = $this->doMatch($session, $def, $matcher);

        // 会主动设置到 session 中.
        if (isset($intent)) {
            $intent = $intent->toInstance($session);
            $session->setMatchedIntent($intent);
        }

        return $intent;
    }


    public function doMatch(Session $session, IntentDefinition $def, IntentMatcher $matcher): ? IntentMessage
    {
        $name = $def->getName();
        // 检查 matched
        $matched = $session->getMatchedIntent();
        if (isset($matched)) {
            return $matched->getName() === $name
                ? $matched
                : null;
        }

        // 检查incoming message
        $incomingMessage = $session->incomingMessage;
        $origin = $incomingMessage->message;

        if ($incomingMessage->hasPossibleIntent($name)) {
            $entities = $incomingMessage->getPossibleIntentEntities($name);
            return $def->newContext($entities);
        }

        // 使用matcher
        $entities = $matcher->match($origin);
        if (isset($entities)) {
            return $def->newContext($entities);
        }

        return null;
    }

}