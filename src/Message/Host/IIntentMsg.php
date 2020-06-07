<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Host;

use Commune\Protocals\HostMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Protocals\HostMsg\IntentMsg;
use Commune\Support\Struct\Struct;
use Commune\Support\Utils\StringUtils;
use Illuminate\Support\Arr;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $intentName
 * @property-read string $textTemplate
 * @property-read string $level
 */
class IIntentMsg extends AbsMessage implements IntentMsg
{
    /*------ 需要配置的常量 ------*/

    const INTENT_NAME = '';
    const DEFAULT_LEVEL = HostMsg::INFO;


    /**
     * @var string
     */
    protected $_text;

    public function __construct(
        string $intentName,
        array $entities = [],
        string $level = null
    )
    {
        if (!empty($intentName)) {
            $entities[self::INTENT_NAME_FIELD] = $intentName;
        }

        if (isset($level)) {
            $entities[self::LEVEL_FIELD] = $level;
        }

        parent::__construct($entities);
    }


    public static function stub(): array
    {
        $intentStub = static::intentStub();
        $stub = [
            self::INTENT_NAME_FIELD => static::INTENT_NAME,
            self::TEMPLATE_FIELD => '',
            self::LEVEL_FIELD => static::DEFAULT_LEVEL
        ];
        return $intentStub + $stub;
    }

    public static function intentStub() : array
    {
        return [];
    }


    public static function relations(): array
    {
        return [];
    }

    public static function create(array $data = []): Struct
    {
        return new static(
            $data[self::INTENT_NAME_FIELD] ?? '',
            $data,
            $data[self::LEVEL_FIELD] ?? null
        );
    }

    public function getRenderId(): string
    {
        return $this->intentName;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getTextTemplate() : string
    {
        $temp = $this->textTemplate;
        return empty($temp)
            ? $this->intentName
            : $temp;
    }

    public function getText(): string
    {
        if (isset($this->_text)) {
            return $this->_text;
        }

        $template = $this->getTextTemplate();
        $slots = $this->getSlots();

        // 不为空则翻译.
        if (empty($slots)) {
            return $template;
        }


        $trans = [];
        foreach ($slots as $key => $val) {

            $replace = '{' . $key . '}';
            $trans[$replace] = $val;
        }

        return $this->_text = str_replace(
            array_keys($trans),
            array_values($trans),
            $template
        );
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function getIntentName(): string
    {
        return $this->intentName;
    }

    public function getEntities(): array
    {
        $data = $this->toArray();
        unset($data[self::INTENT_NAME_FIELD]);
        unset($data[self::TEMPLATE_FIELD]);
        unset($data[self::LEVEL_FIELD]);
        return $data;
    }


    public function getSlots(): array
    {
        $values = $this->toArray();
        $flattenSlots = Arr::dot($values);
        $slots = array_filter($flattenSlots, function($value) {
            return is_scalar($value) || StringUtils::isString($value);
        });
        return array_map('strval', $slots);
    }

    public function __toString()
    {
        return $this->getText();
    }

}