<?php


namespace Commune\Chatbot\OOHost\Context\Intent;


use Commune\Chatbot\Framework\Utils\StringUtils;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\SelfRegister;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\NLU\NLUExample;

abstract class AbsCmdIntent extends AbsIntent implements SelfRegister
{
    /**
     * @var string 简介.
     */
    const DESCRIPTION = 'should define intent description by constant';

    /**
     * @var string[] 例句.
     * entity 用markdown link 语法标记.
     * 例如: 请问[北京](city)的天气如何
     */
    const EXAMPLES = [];

    abstract public static function getMatcherOption() : IntentMatcherOption;

    final public function getName(): string
    {
        return static::getContextName();
    }

    public static function __depend(Depending $depending): void
    {
        $option = static::getMatcherOption();
        if (!empty($option->signature)) {
            $depending->onSignature($option->signature);
        }
    }

    /**
     * @return IntentDefinition
     */
    public function getDef() : Definition
    {
        $repo = static::getRegistrar();
        $name = $this->getName();

        if ($repo->has($name)) {
            return $repo->get($name);
        }
        $def = static::buildDefinition();
        $repo->register($def);

        // 注册 nlu. 非强制, 可以被 intentManager 改写.
        if (!empty(static::EXAMPLES)) {
            foreach (static::EXAMPLES as $str) {
                $repo->registerNLUExample(
                    $name,
                    new NLUExample($str)
                );
            }
        }
        return $def;
    }


    public static function registerSelfDefinition(): void
    {
        $def = static::buildDefinition();
        $repo = static::getRegistrar();
        $repo->register($def);
    }


    protected static function getContextName() : string
    {
        return StringUtils::namespaceSlashToDot(static::class);
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