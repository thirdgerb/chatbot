<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IMindDef;

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Exceptions\Runtime\BrokenSessionException;
use Commune\Blueprint\Framework\Command\CommandDef;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\MindDef\Intent\ExampleEntity;
use Commune\Blueprint\Ghost\MindDef\Intent\IntentExample;
use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Framework\Command\ICommandDef;
use Commune\Ghost\IMindDef\Intent\IIntentExample;
use Commune\Message\Host\IIntentMsg;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;
use Commune\Protocals\HostMsg\IntentMsg;
use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Support\Option\AbsOption;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;
use Commune\Support\Struct\Struct;
use Commune\Support\Utils\ArrayUtils;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name
 * @property-read string $title
 * @property-read string $desc
 *
 * ## 意图内容.
 * @property-read string[] $examples
 * @property-read string[] $entityNames
 *
 * ## 匹配规则
 * @property-read string|null $alias
 * @property-read string $spell
 * @property-read string $signature
 * @property-read string[] $keywords
 * @property-read string[] $regex
 * @property-read string[] $ifEntity
 *
 * @property-read string|null $matcher
 *
 * @property-read string|null $messageWrapper
 */
class IIntentDef extends AbsOption implements IntentDef
{

    /**
     * @var IntentExample[]
     */
    protected $exampleObjects = [];

    /**
     * @var CommandDef|null
     */
    protected $commandDef;

    /**
     * @var string[]
     */
    protected $_entityNames;

    public static function stub(): array
    {
        return [
            // 意图的名称
            'name' => '',

            // 意图的标题.
            'title' => '',

            // 意图的简介. 可以作为选项的内容.
            'desc' => '',

            // 意图的别名. 允许别名中的意图作为精确匹配规则.
            'alias' => null,

            // 意图的精确命中命令.
            'spell' => '',

            // 例句, 用 []() 标记, 例如 "我想知道[北京](city)[明天](date)天气怎么样"
            'examples' => [],

            // 作为命令.
            'signature' => '',

            // entityNames
            'entityNames' => [],

            // 关键字
            'keywords' => [],

            // 正则
            'regex' => [],

            // 命中任意 entity
            'ifEntity' => [],

            // 自定义校验器. 字符串, 通常是类名或者方法名.
            'matcher' => null,

            // 用于包装 IntentMsg 的方法.
            'messageWrapper' => null,
        ];
    }

    public static function toMetaConfig(array $data) : array
    {
        unset($data['name']);
        unset($data['title']);
        unset($data['desc']);
        return $data;
    }

    public static function relations(): array
    {
        return [];
    }

    /*---------- parse ----------*/
    public function parseEntities(array $values) : array
    {
        return ArrayUtils::parseValuesByKeysWithListMark(
            $values,
            $this->getEntityNames(),
            true
        );
    }


    public function getEntityNames(): array
    {
        if (isset($this->_entityNames)) {
            return $this->_entityNames;
        }

        // 最好手动定义 entity 字段名
        $names = $this->entityNames;
        if (!empty($names)) {
            return $this->_entityNames = $names;
        }

        // 如果没有定义, 则会遍历所有的 example, 提取出可能的 entity name
        $examples = $this->getExampleObjects();
        if (empty($examples)) {
            return $this->_entityNames = [];
        }

        // 检查是否同一个 entity 在同一个例子里出现过两次, 如果出现过则意味着是 list entity
        // 否则默认都当作 unique entity, 一个句子里只会出现一次.
        $namesCounts = array_reduce(
            $examples,
            function(array $valueCounts, IntentExample $example) {

                $names = array_map(function(ExampleEntity $entity) {
                    return $entity->name;
                }, $example->getEntities());

                $counts = ArrayUtils::valueCount($names);

                return ArrayUtils::mergeMapByMaxVal($valueCounts, $counts);
            },
            []
        );

        // 所有 list entity 会用 "[]" (list mark) 在结尾标记.
        return $this->_entityNames = ArrayUtils::uniqueValuesWithListMark($namesCounts);
    }


    /*---------- properties ----------*/

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->desc;
    }

    public function getIntentName(): string
    {
        $alias = $this->alias;
        return $alias ?? $this->name;
    }

    public function getCommandDef(): ? CommandDef
    {
        if (empty($this->signature)) {
            return null;
        }

        return $this->commandDef
            ?? $this->commandDef = ICommandDef::makeBySignature($this->signature);
    }

    public function getKeywords(): array
    {
        return $this->keywords;
    }

    public function getRegex(): array
    {
        return $this->regex;
    }

    /*---------- match ----------*/

    public function match(Cloner $cloner): bool
    {
        $input = $cloner->input;

        $selfName = $this->getName();

        // 检查消息体本身.
        $message = $input->getMessage();
        if ($message instanceof IntentMsg) {
            return $message->getIntentName() === $selfName;
        }

        // 检查理解模块的结果.
        $intention = $input->comprehension->intention;
        if ($intention->hasPossibleIntent($selfName)) {
            return true;
        }

        // 别名检查.
        $alias = $this->alias;
        if (!empty($alias)) {
            $reg = $cloner->mind->intentReg();
            $matched = $reg->hasDef($alias)
                ? $reg->getDef($alias)->match($cloner)
                : $intention->hasPossibleIntent($alias, true);

            if ($matched) {
                return true;
            }
        }

        // 自定义的匹配器优先级相对高.
        $selfMatcher = $this->matcher;
        if (isset($selfMatcher)) {
            if (
                is_string($selfMatcher)
                && class_exists($selfMatcher)
                && method_exists($selfMatcher, '__invoke')
            ) {
                $selfMatcher = [$selfMatcher, '__invoke'];
            }

            try {
                $predict = $cloner->container->call($selfMatcher, ['intentDef' => $this]);
                return $predict === true;

            } catch (\Exception $e) {
                throw new BrokenSessionException(
                    "invalid intent matcher",
                    $e
                );
            }
        }

        // 剩下的匹配逻辑都是针对文本的.
        if (!$message instanceof VerbalMsg) {
            return false;
        }

        $text = $message->getText();
        $text = StringUtils::normalizeString($text);
        $spell = $this->spell;

        if (!empty($spell) && $text === $spell) {
            return true;
        }

        // 正则
        $regex = $this->getRegex();
        if (!empty($regex)) {
            foreach ($regex as $pattern) {
                if (preg_match($pattern, $text)) {
                    return true;
                }
            }
        }

        // 命令
        $cmdDef = $this->getCommandDef();
        if (!empty($cmdDef)) {
            if ($cmdDef->parseCommandMessage($text)->isCorrect()) {
                return true;
            }
        }

        $ifAnyEntities = $this->ifEntity;
        if (!empty($ifAnyEntities)) {
            foreach ($ifAnyEntities as $entityName) {
                if ($intention->hasEntity($entityName)) {
                    return true;
                }
            }
        }

        // 关键词
        $keywords = $this->getKeywords();
        if (!empty($keywords) && StringUtils::expectKeywords($text, $keywords, true)) {
            return true;
        }


        return false;
    }

    public function toIntentMessage(Cloner $cloner): IntentMsg
    {
        $wrapper = $this->messageWrapper ?? IIntentMsg::class;
        $entities = $cloner->input
            ->comprehension
            ->intention
            ->getIntentEntities($this->getName());
        $data = $this->parseEntities($entities);
        $data[IntentMsg::INTENT_NAME_FIELD] = $this->getName();

        return call_user_func([$wrapper, Struct::CREATE_FUNC], $data);
    }


    /*---------- example ----------*/

    public function getExamples(): array
    {
        return $this->examples;
    }

    public function getExampleObjects(): array
    {
        return $this->exampleObjects
            ?? $this->exampleObjects = array_map(
                function(string $example){
                    return new IIntentExample($example);
                },
                $this->examples
            );
    }


    /*---------- meta ----------*/

    /**
     * @return IntentMeta
     */
    public function toMeta(): Meta
    {
        $data = [];
        $config = $this->toArray();

        $data['name'] = $config['name'];
        $data['title'] = $config['title'];
        $data['desc'] = $config['desc'];
        $data['wrapper'] = static::class;
        $data['config'] = static::toMetaConfig($config);

        return new IntentMeta($data);
    }

    /**
     * @param IntentMeta $meta
     * @return static
     */
    public static function wrapMeta(Meta $meta): Wrapper
    {
        if (!$meta instanceof IntentMeta) {
            throw new InvalidArgumentException(
                "only accept subclass of " . IntentMeta::class
            );
        }
        $config = $meta->config;
        $config['name'] = $meta->name;
        $config['title'] = $meta->title;
        $config['desc'] = $meta->desc;

        return new static($config);
    }

}