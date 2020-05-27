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
use Commune\Blueprint\Ghost\MindDef\Intent\IntentExample;
use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Framework\Command\ICommandDef;
use Commune\Ghost\IMindDef\Intent\IIntentExample;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;
use Commune\Protocals\HostMsg\IntentMsg;
use Commune\Support\Option\AbsOption;
use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $name
 * @property-read string $title
 * @property-read string $desc
 *
 * ## 意图内容.
 * @property-read string[] $examples
 *
 * ## 匹配规则
 * @property-read string $signature
 * @property-read string[] $keywords
 * @property-read string[] $regex
 * @property-read string[] $ifEntity
 *
 * @property-read string|null $matcher
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

    public static function stub(): array
    {
        return [
            // 意图的名称
            'name' => '',
            // 意图的标题, 应允许用标题来匹配.
            'title' => '',
            // 意图的简介. 可以作为选项的内容.
            'desc' => '',
            // 例句, 用 []() 标记, 例如 "我想知道[北京](city)[明天](date)天气怎么样"
            'examples' => [],
            // 作为命令.
            'signature' => '',

            // 关键字
            'keywords' => [],
            // 正则
            'regex' => [],

            // 命中任意 entity
            'anyEntity' => [],
            // 自定义校验器. 字符串, 通常是类名或者方法名.
            'matcher' => null,
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    /*---------- properties ----------*/

    public function getName(): string
    {
        return $this->name;
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

        // 检查消息体本身.
        $message = $input->getMessage();
        if ($message instanceof IntentMsg) {
            return $message->getIntentName() === $this->getName();
        }

        // 检查理解模块的结果.
        $intention = $input->comprehension->intention;
        if ($intention->hasPossibleIntent($this->getName())) {
            return true;
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
                return (bool) $cloner->container->call($selfMatcher);
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

        $matcher = $cloner->matcher->refresh();

        if ($matcher->is($this->getTitle())->truly()) {
            $matcher->refresh();
            return true;
        }

        if ($matcher->isAnswer($this->getDescription())->truly()) {
            $matcher->refresh();
            return true;
        }

        // 正则
        $regex = $this->getRegex();
        if (!empty($regex)) {
            foreach ($regex as $pattern) {
                if ($matcher->pregMatch($pattern)->truly()) {
                    $matcher->refresh();
                    return true;
                }
            }
        }

        // 命令
        $cmdDef = $this->getCommandDef();
        if (!empty($cmdDef)) {
            if ($matcher->matchCommandDef($cmdDef, false)->truly()) {
                $matcher->refresh();
                return true;
            }
        }

        // 关键词
        $keywords = $this->getKeywords();
        if (!empty($keywords)) {
            if ($matcher->hasKeywords($keywords, [], true)->truly()) {
                $matcher->refresh();
                return true;
            }
        }

        return false;
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

    public function getMeta(): Meta
    {
        $config = $this->toArray();
        unset($config['name']);
        unset($config['title']);
        unset($config['desc']);

        return new IntentMeta([
            'name' => $this->name,
            'title' => $this->title,
            'desc' => $this->desc,
            'wrapper' => static::class,
            'config' => $config
        ]);
    }

    /**
     * @param IntentMeta $meta
     * @return static
     */
    public static function wrap(Meta $meta): Wrapper
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