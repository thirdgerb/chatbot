<?php


namespace Commune\Chatbot\OOHost\Context\Intent;


use Commune\Chatbot\Framework\Utils\ValidateUtils;
use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\NLU\NLUExample;
use Commune\Chatbot\OOHost\Session\Session;
use Illuminate\Support\Collection;

/**
 * @method  IntentDefinition|null get(string $contextName) : ? Definition
 */
class IntentRegistrar extends ContextRegistrar implements Registrar
{
    const DEF_CLAZZ = IntentDefinition::class;

    /**
     * @var IntentMatcher[]
     */
    protected $matchers = [];

    /**
     * @var NLUExample[]
     */
    protected $nluExamples = [];

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

    /**
     * 注册一个intentMatcher
     * @param string $intentName
     * @param IntentMatcherOption $option
     */
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

    /**
     * 获取intentMatcher
     * @param string $intentName
     * @return IntentMatcher|null
     */
    public function getMatcher(string $intentName) : ?  IntentMatcher
    {
        $intentName = $this->filterFetchId($intentName);
        return $this->matchers[$intentName] ?? null;
    }

    /**
     * 根据有可能存在的intent, 进行匹配.
     * @param Session $session
     * @return IntentMessage|null
     */
    public function matchHighlyPossibleIntent(Session $session) : ? IntentMessage
    {
        $matched = $session->getMatchedIntent();
        if (isset($matched)) {
            return $matched;
        }

        $incomingMessage = $session->incomingMessage;

        $names = $incomingMessage->getHighlyPossibleIntentNames();
        if (empty($names)) {
            return null;
        }

        $first = $names[0];
        // 按优先级顺序进行遍历.
        foreach ($names as $name) {
            if ($this->has($name)) {
                $intent = $this->get($name)->newContext(
                    $incomingMessage->getPossibleIntentEntities($name)
                );
                $session->setMatchedIntent($intent);
                return $intent;
            }
        }

        // 如果都没有注册的, 则用个占位符.
        return new PlaceHolderIntent(
            $first,
            $incomingMessage->getPossibleIntentEntities($first)
        );

    }

    /**
     * 按intent name 进行匹配.
     * @param string $intentName
     * @param Session $session
     * @return IntentMessage|null
     */
    public function matchIntent(string $intentName, Session $session) : ? IntentMessage
    {
        if (! $this->has($intentName)) {
            return null;
        }

        // 必须用definition, 因为intentName 可能有很多种...
        // 牺牲一点性能获取工程上的便利.
        // 但有可能造成歧义. 需要继续权衡.
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

    /**
     * @param string $intentName
     * @param NLUExample $example
     * @param bool $force
     */
    public function registerNLUExample(string $intentName, NLUExample $example, bool $force = false) : void
    {
        $text = $example->text;
        if ($force || !isset($this->nluExamples[$intentName][$text])) {
            // 避免重复. 但成本比较高.
            $this->nluExamples[$intentName][$example->text] = $example;
        }
    }


    /**
     * @param string $intentName
     * @return NLUExample[]
     */
    public function getNLUExamplesByIntentName(string $intentName) : array
    {
        if (!$this->has($intentName)) {
            return [];
        }
        $data =  $this->nluExamples[$intentName] ?? [];
        return array_values($data);
    }

    /**
     * @param string $domain
     * @return array
     */
    public function getNLUExampleMapByIntentDomain(string $domain = '') : array
    {
        $names = $this->getNamesByDomain($domain);

        $result = [];
        foreach ($names as $name) {
            $examples = $this->getNLUExamplesByIntentName($name);
            if (!empty($examples)) {
                $result[$name] = $examples;
            }
        }
        return $result;
    }

    /**
     * @return Collection
     */
    public function getNLUExamplesCollection() : Collection
    {
        $items = [];
        foreach ($this->nluExamples as $name => $examples) {
            if (!empty($examples) && $this->has($name)) {
                $items[$name] = $examples;
            }
        }

        return new Collection($items);
    }

    public function countIntentsHasNLUExamples(): int
    {
        return count($this->nluExamples);
    }


    public function countNLUExamples(string $domain = null): int
    {
        $examples = $this->getNLUExampleMapByIntentDomain($domain ?? '');
        return array_reduce($examples, function($c, $i){
            return $c + count($i);
        }, 0);
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
        $incomingMessage = $session->incomingMessage;

        // 必须高于阈值的意图才会被识别.
        // 是否已经包含.
        if ($incomingMessage->hasHighlyPossibleIntent($name)) {
            $entities = $incomingMessage->getPossibleIntentEntities($name);
            return $def->newContext($entities)->toInstance($session);
        }

        // 使用matcher
        $origin = $incomingMessage->message;
        $entities = $matcher->match($origin);
        if (isset($entities)) {
            return $def->newContext($entities)->toInstance($session);
        }

        return null;
    }


    public function setIntentNLUExamples(string $intentName, array $examples): void
    {
        $items = [];
        foreach ($examples as $example) {
            if (!$example instanceof NLUExample) {
                throw new \InvalidArgumentException(
                    __METHOD__
                    . ' only accept list of '
                    . NLUExample::class
                    . ', '
                    . ValidateUtils::getTypeOf($example)
                    . ' given'
                );
            }
            $items[$example->text] = $example;
        }
        $this->nluExamples[$intentName] = $items;
    }

    public function hasCommandIntent(string $intentName) : bool
    {
        if (!$this->has($intentName)) {
            return false;
        }
        $matcher = $this->getMatcher($intentName);
        return $matcher->hasCommand();
    }


}