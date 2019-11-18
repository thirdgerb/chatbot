<?php


namespace Commune\Chatbot\OOHost\Context\Intent;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerbalMsg;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Utils\CommandUtils;
use Commune\Chatbot\OOHost\Command\CommandDefinition;
use Commune\Chatbot\OOHost\Command\CommandMessage;

/**
 * 成本较高的意图判断策略. 用php实现.
 *
 * @property-read string $intentName
 * @property-read array $regex
 * @property-read array $keywords
 * @property-read string $signature
 * @property-read CommandDefinition|null $command
 */
class IntentMatcher
{
    /**
     * @var string
     */
    protected $intentName;

    /**
     * @var string
     */
    protected $signature = '';

    /**
     * @var array  [int][pattern, ...matches]
     */
    protected $regex = [];

    /**
     * @var CommandDefinition|null
     */
    protected $command;

    /**
     * @var array [ 'word1', 'word2', ['synonym1', 'synonym2']]
     */
    protected $keywords = [];

    /**
     * IntentMatcher constructor.
     * @param $intentName
     */
    public function __construct($intentName)
    {
        $this->intentName = $intentName;
    }

    public function toOption() : IntentMatcherOption
    {
        return new IntentMatcherOption([
            'signature' => $this->signature,
            'keywords' => $this->keywords,
            'regex' => array_map(function(array $regex) {
                return array_unshift($regex[1], $regex[0]);
            }, $this->regex),
        ]);
    }

    public function mergeOption(IntentMatcherOption $option)
    {
        if (!empty($option->signature)) {
            $this->setSignature($option->signature);
        }

        if (!empty($option->keywords)) {
            $this->setKeywords($option->keywords);
        }

        if (!empty($option->regex)) {
            foreach ($option->regex as $keys) {
                $pattern = array_shift($keys);
                $this->addRegex($pattern, $keys);
            }
        }
    }

    /**
     * @param string $signature
     */
    public function setSignature(string $signature) : void
    {
        $def = CommandDefinition::makeBySignature($signature);

        if (empty($def) || empty($def->getCommandName())) {
            throw new ConfigureException(
                'intent '
                . $this->intentName
                . ' has bad command signature '
                . $signature
            );
        }

        $this->signature = $signature;
        $this->command = $def;
    }

    public function setKeywords(array $keywords) : void
    {
        $this->keywords = $keywords;
    }

    public function addRegex(string $pattern, array $matches) : void
    {
        $this->regex[] = [$pattern, $matches];
    }

    public function match(Message $message) : ? array
    {
        // 不用匹配. 理论上进不来这里.
        if ($message instanceof IntentMessage) {
            return null;
        }

        // 硬生生地用闭包去匹配. 希望不要用.
        if (!empty($this->callers)) {
            foreach ($this->callers as $caller) {
                $entities = $caller($message);
                if (isset($entities)) {
                    return $entities;
                }
            }
        }

        // 其它的逻辑都要求是对话, 才去匹配.
        if (!$message instanceof VerbalMsg) {
            return null;
        }

        $text = $message->getTrimmedText();
        // 检查命令模式.
        if ($this->hasCommand()) {
            $entities = $this->matchCommand($message, $this->command);
            if (isset($entities)) {
                return $entities->getEntities();
            }
        }

        // 检查正则模式.
        if (!empty($this->regex)) {
            $matchedEntities = [];
            $matched = false;
            // 所有正则都会过一遍. 可以分布添加.
            foreach ($this->regex as list($pattern, $args)) {
                $entities = $this->matchRegex($text, $pattern, $args);
                if (isset($entities)) {
                    $matched = true;
                    $matchedEntities = $entities + $matchedEntities;
                }
            }
            return $matched ? $matchedEntities : null;
        }

        // 勉强检查一下关键字.
        if (!empty($this->keywords)) {
            return $this->matchWords($text, $this->keywords) ? [] : null;
        }

        return null;
    }

    public function hasCommand() : bool
    {
        return !empty($this->command);
    }

    public function getCommand() : ? CommandDefinition
    {
        return $this->command;
    }

    public static function matchRegex(string $text, string $pattern, array $args) : ? array
    {
        $matches = [];
        $matched = preg_match($pattern, $text, $matches);

        if (!$matched) {
            return null;
        }

        array_shift($matches);
        $entities = [];
        foreach ($args as $index => $name) {
            $entities[$name][] = $matches[$index] ?? null;
        }

        $result = [];
        foreach ($entities as $name => $values) {
            if (empty($values)) {
                $result[$name] = null;

            } elseif (count($values) === 1) {
                $result[$name] = current($values);

            } else {
                $result[$name] = array_filter($values, function($v){
                    return isset($v);
                });
            }

        }

        return $result;
    }

    public static function matchCommand(
        Message $message,
        CommandDefinition $definition
    ) : ? CommandMessage
    {

        $cmdStr = $message->getCmdText();

        // 不是命令类型.
        if (empty($cmdStr)) {
            return null;
        }

        $commandName = $definition->getCommandName();
        if (CommandUtils::matchCommandName($cmdStr, $commandName)) {
            return $definition->toCommandMessage($cmdStr, $message);
        }

        return null;
    }

    /**
     *
     * @param string $text
     * @param array $keywords
     * @param bool $any
     * @return bool
     */
    public static function matchWords(
        string $text,
        array $keywords,
        bool $any = false
    ) : bool
    {
        if (empty($keywords)) {
            return false;
        }

        if (empty($text)) {
            return false;
        }

        foreach ($keywords as $keyword) {

            if (is_array($keyword)) {
                // 只要存在一个. 同义词.
                $matched = self::matchWords($text, $keyword, true);

            } else {
                // 判断关键字是否存在.
                $matched = is_int(mb_strpos($text, $keyword));
            }

            if ($any && $matched) {
                return true;
            }

            if (!$any && !$matched) {
                return false;
            }
        }

        return !$any;
    }

    public function __get($name)
    {
        return $this->{$name};
    }
}
