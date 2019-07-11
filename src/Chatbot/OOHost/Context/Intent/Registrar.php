<?php


namespace Commune\Chatbot\OOHost\Context\Intent;

use Commune\Chatbot\OOHost\Context\Registrar as ContextRegistrar;
use Commune\Chatbot\OOHost\NLU\NLUExample;
use Commune\Chatbot\OOHost\Session\Session;
use Illuminate\Support\Collection;

interface Registrar extends ContextRegistrar
{

    /*---------- matcher ----------*/

    /**
     * 注册一个intentMatcher
     *
     * @param string $intentName
     * @param IntentMatcherOption $option
     */
    public function registerMatcher(
        string $intentName,
        IntentMatcherOption $option
    ) : void;


    /**
     * 获取intentMatcher
     *
     * @param string $intentName
     * @return IntentMatcher|null
     */
    public function getMatcher(string $intentName) : ?  IntentMatcher;

    /*---------- match ----------*/

    /**
     * 根据有可能存在的intent, 进行匹配.
     *
     * @param Session $session
     * @return IntentMessage|null
     */
    public function matchHighlyPossibleIntent(Session $session) : ? IntentMessage;

    /**
     * 按intent name 进行匹配.
     * @param string $intentName
     * @param Session $session
     * @return IntentMessage|null
     */
    public function matchIntent(string $intentName, Session $session) : ? IntentMessage;


    /**
     * intent 可以用命令的方式匹配.
     * @param string $intentName
     * @return bool
     */
    public function hasCommandIntent(string $intentName) : bool;

    /*---------- NLU ----------*/

    /**
     * 注册NLU examples
     * @param string $intentName
     * @param NLUExample $example
     * @param bool $force
     */
    public function registerNLUExample(string $intentName, NLUExample $example, bool $force = false) : void;

    /**
     * @param string $intentName
     * @return NLUExample[]
     */
    public function getNLUExamplesByIntentName(string $intentName) : array;

    /**
     * 根据domain, 获取 intentName =>
     * @param string $domain
     * @return array  [ 'name' => [ NLUExample $example1] ]
     */
    public function getNLUExampleMapByIntentDomain(string $domain = '') : array;

    /**
     * 计算包含 nlu examples 的 intents 数量.
     * @return int
     */
    public function countIntentsHasNLUExamples() : int;

    /**
     * 计算一个domain 下examples 的数量.
     * @param string|null $domain
     * @return int
     */
    public function countNLUExamples(string $domain = null) : int;

    /**
     * 获取所有的 nlu examples,  intentName => NluExamples[]
     * @return Collection
     */
    public function getNLUExamplesCollection() : Collection;

    /**
     * 强制赋值, 改变已有的 nlu examples
     * @param string $intentName
     * @param NLUExample[] $examples
     */
    public function setIntentNLUExamples(string $intentName, array $examples) : void;

}