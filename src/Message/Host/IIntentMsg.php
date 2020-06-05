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
        array $slots = [],
        string $level = null
    )
    {
        if (!empty($intentName)) {
            $slots['intentName'] = $intentName;
        }

        if (isset($level)) {
            $slots['level'] = $level;
        }

        parent::__construct($slots);
    }


    public static function stub(): array
    {
        $intentStub = static::intentStub();
        $stub = [
            'intentName' => static::INTENT_NAME,
            'textTemplate' => '',
            'level' => static::DEFAULT_LEVEL
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
            $data['id'] ?? '',
            $data,
            $data['level'] ?? null
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

        $flattenSlots = Arr::dot($slots);

        $trans = [];
        foreach ($flattenSlots as $key => $val) {

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

    public function getSlots(): array
    {
        return $this->toArray();
    }

    public function __toString()
    {
        return $this->getText();
    }

}