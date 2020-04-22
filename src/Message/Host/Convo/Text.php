<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Host\Convo;

use Commune\Protocals\Host\Convo\VerbalMsg;
use Commune\Protocals\HostMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Support\Struct\Struct;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $text          文本正文
 * @property-read string $level         消息的级别.
 */
class Text extends AbsMessage implements VerbalMsg
{

    public function __construct($text = '', string $level = HostMsg::INFO)
    {
        parent::__construct([
            'text' => (string) $text,
            'level' => $level
        ]);
    }

    public static function create(array $data = []): Struct
    {
        return new static(
            $data['text'] ?? '',
            $data['level'] ?? HostMsg::INFO
        );
    }

    public static function stub(): array
    {
        return [
            'text' => 'hello world!',
            'level' => HostMsg::INFO,
        ];
    }

    public function isEmpty(): bool
    {
        return empty($this->_data['text']);
    }

    public function getTrimmedText(): string
    {
        return StringUtils::trim($this->text);
    }


    public static function relations(): array
    {
        return [];
    }

    public static function validate(array $data): ? string
    {
        $valid = is_string($data['text'] ?? null)
            && in_array($data['level'] ?? null, HostMsg::LEVELS);

        return $valid ? null : 'invalid type';
    }


}