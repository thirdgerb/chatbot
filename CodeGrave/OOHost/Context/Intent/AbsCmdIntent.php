<?php


namespace Commune\Chatbot\OOHost\Context\Intent;


use Commune\Chatbot\OOHost\Context\Contracts\RootIntentRegistrar;
use Commune\Support\Utils\StringUtils;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\SelfRegister;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Container\ContainerContract;

/**
 * Intent 一种更具体的实现.
 * 加入了 command, regex, keywords 等 matcher 基本功能的定义策略.
 * 也允许 selfRegister
 */
abstract class AbsCmdIntent extends AbsIntent implements SelfRegister
{
    /**
     * @var string 简介.
     */
    const DESCRIPTION = 'should define intent description by constant';

    /**
     * 命令名. 可以用命令的方式来匹配
     * 写法 @see IntentMatcherOption
     */
    const SIGNATURE = '';

    /**
     * 用正则来匹配
     * 写法 @see IntentMatcherOption
     */
    const REGEX = [];

    /**
     * 用关键字来匹配
     * 写法 @see IntentMatcherOption
     */
    const KEYWORDS = [];

    /**
     * 定义 PHP 匹配规则的地方.
     * 修改这个方法, 可以真正修改所有自定义匹配规则
     *
     * @return IntentMatcherOption
     */
    public static function getMatcherOption(): IntentMatcherOption
    {
        return new IntentMatcherOption([
            'signature' => static::SIGNATURE,
            'regex' => static::REGEX,
            'keywords' => static::KEYWORDS,
        ]);
    }

    final public function getName(): string
    {
        return static::getContextName();
    }


    /**
     * @return IntentDefinition
     */
    public function getDef() : Definition
    {
        $repo = $this->getSession()->intentRepo;
        $name = $this->getName();

        if (!$repo->hasDef($name)) {
            $repo->registerDef(static::buildDefinition());
        }
        return $repo->getDef($name);
    }

    public static function registerSelfDefinition(ContainerContract $processContainer): void
    {
        $repo = $processContainer->get(RootIntentRegistrar::class);
        // 强制覆盖.
        $repo->registerDef(static::buildDefinition(), true);
    }


    public static function getContextName() : string
    {
        return StringUtils::normalizeContextName(static::class);
    }

    protected static function buildDefinition(): Definition
    {
        $intentOption = static::getMatcherOption();
        $def = new IntentDefinitionImpl(
            static::getContextName(),
            static::class,
            static::DESCRIPTION,
            $intentOption
        );
        return $def;
    }

    abstract public function navigate(Dialog $dialog): ? Navigator;

}