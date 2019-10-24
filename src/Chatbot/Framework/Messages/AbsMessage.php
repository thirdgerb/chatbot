<?php

/**
 * Class Message
 * @package Commune\Chatbot\Framework\Message
 */

namespace Commune\Chatbot\Framework\Messages;


use Carbon\Carbon;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\Tags\Transformed;
use Commune\Chatbot\Framework\Utils\CommandUtils;
use Commune\Support\Utils\ArrayUtils;
use Commune\Support\Utils\StringUtils;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Chatbot\Blueprint\Message\Message as Contract;

/**
 * 所有消息的基础抽象.
 *
 * Class Message
 * @package Commune\Chatbot\Framework\Message
 */
abstract class AbsMessage implements Contract
{
    use ArrayAbleToJson;

    /**
     * @var int
     */
    protected $_createdAt;

    /**
     * @var Carbon
     */
    protected $_createdAt_carbon;

    /**
     * @var int|null
     */
    protected $_deliverAt;

    /**
     * @var Carbon
     */
    protected $_deliverAt_carbon;

    /**
     * text 转义成 命令的形式. 如果不是命令, 则返回null
     * @var string|null|false
     */
    protected $_cmdText;

    /**
     * @var string|null
     */
    protected $_trimmed;


    /**
     * 可以做依赖注入的对象名
     * @var string[][]
     */
    private static $dependencyNames = [];

    /**
     * AbsMessage constructor.
     * @param Carbon $createdAt
     */
    public function __construct(Carbon $createdAt = null)
    {
        $this->_createdAt_carbon = $createdAt ?? new Carbon();
        $this->_createdAt = $this->_createdAt_carbon->timestamp;
    }

    public function __sleep() : array
    {
        return [
            '_deliverAt',
            '_createdAt',
        ];
    }

    public function toMessageData(): array
    {
        $fields = $this->__sleep();
        $result = [];
        foreach ($fields as $name) {
            $result[$name] = $this->{$name};
        }
        return ArrayUtils::recursiveToArray($result);
    }

    final public function namesAsDependency(): array
    {
        $class = static::class;
        if (isset(self::$dependencyNames[$class])) {
            return self::$dependencyNames[$class];
        }

        $r = new \ReflectionClass($class);

        // 当前类名
        $names[] = $r->getName();
        // 根 message 类名
        $names[] = Message::class;
        // 所有 interface 里继承 message 的.
        foreach ( $r->getInterfaces() as $interfaceReflect ) {
            if ($interfaceReflect->isSubclassOf(Message::class)) {
                $names[] = $interfaceReflect->getName();
            }
        }

        // 抽象父类.
        do  {
            if ($r->isAbstract()) {
                $names[] = $r->getName();
            }


        } while ($r = $r->getParentClass());

        sort($names);
        return self::$dependencyNames[$class] = $names;
    }


    /*------- methods -------*/


    /**
     * 默认的数据结构.
     *
     * @return array
     */
    public function toArray() : array
    {
        $data =  [
            'type' => get_class($this),
            'data' => $this->toMessageData(),
        ];

        if ($this instanceof Transformed) {
            $data['origin'] = $this->getOriginMessage()->toArray();
        }
        return $data;
    }

    /**
     * 去掉了多余格式后的文本.
     * @return string
     */
    public function getTrimmedText() : string
    {
        if (isset($this->_trimmed)) {
            return $this->_trimmed;
        }
        $text = $this->getText();

        // 去掉全角符号, 降低复杂性.
        // 这里不能无关大小写, 否则会特别麻烦.
        $text = StringUtils::sbc2dbc($text);
        return $this->_trimmed = trim($text, static::TRIMMING_MARKS);
    }


    public function getCmdText(): ? string
    {
        if ($this->_cmdText === false) {
            return null;
        }

        if (isset($this->_cmdText)) {
            return $this->_cmdText;
        }

       $this->_cmdText = CommandUtils::getCommandStr(
           $this->getTrimmedText(),
           // 确保 commandUtils 会使用 user command mark
           null
       ) ?? false;

        return $this->getCmdText();
    }

    public function setCmdText(string $text = null): void
    {
        $this->_cmdText = $text;
    }

    public function __toString()
    {
        return $this->getText();
    }

    public function getDeliverAt(): ? Carbon
    {
        if (isset($this->_deliverAt_carbon)) {
            return $this->_deliverAt_carbon;
        }

        if (!isset($this->_deliverAt)) {
            return null;
        }

        return $this->_deliverAt_carbon = Carbon::createFromTimestamp($this->_deliverAt);
    }

    /**
     * @param Carbon $carbon
     * @return static
     */
    public function deliverAt(Carbon $carbon): Message
    {
        $this->_deliverAt_carbon = $carbon;
        $this->_deliverAt = $carbon->timestamp;
        return $this;
    }


    public function getCreatedAt(): Carbon
    {
        if (isset($this->_createdAt_carbon)) {
            return $this->_createdAt_carbon;
        }

        return $this->_createdAt_carbon = Carbon::createFromTimestamp($this->_createdAt ?? time());
    }

}