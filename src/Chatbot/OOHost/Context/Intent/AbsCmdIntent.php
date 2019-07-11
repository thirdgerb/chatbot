<?php


namespace Commune\Chatbot\OOHost\Context\Intent;


use Commune\Chatbot\Framework\Utils\StringUtils;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\SelfRegister;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\NLU\NLUExample;

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


    // 命令名. 可以用命令的方式来匹配
    const SIGNATURE = '';
    // 用正则来匹配
    const REGEX = [];
    // 用关键字来匹配.
    const KEYWORDS = [];

    /**
     * @var string[] 例句.
     * entity 用markdown link 语法标记.
     * 例如: 请问[北京](city)的天气如何
     */
    const EXAMPLES = [];


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
        $repo = static::getRegistrar();
        $name = $this->getName();

        if (!$repo->has($name)) {
            static::registerSelfDefinition();
        }
        return $repo->get($name);
    }


    public static function registerSelfDefinition(): void
    {
        $def = static::buildDefinition();
        $repo = static::getRegistrar();
        // 强制.
        $repo->register($def, true);

        // 注册 nlu. 非强制, 可以被 intentManager 改写.
        if (!empty(static::EXAMPLES)) {
            foreach (static::EXAMPLES as $str) {
                $repo->registerNLUExample(
                    static::getContextName(),
                    new NLUExample($str)
                );
            }
        }
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