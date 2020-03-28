<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Prototype\Convo;

use Commune\Message\Blueprint\Convo\TextMsg;
use Commune\Message\Prototype\AMessage;
use Commune\Support\Babel\BabelSerializable;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IText extends AMessage implements TextMsg
{

    /**
     * @var string
     */
    protected $text;

    /**
     * @var string
     */
    protected $level;

    /**
     * IText constructor.
     * @param string $text
     * @param string $level
     * @param float|null $createdAt
     */
    public function __construct(string $text, string $level = TextMsg::INFO, float $createdAt = null)
    {
        $this->text = $text;
        $this->level = $level;
        parent::__construct($createdAt);
    }


    public static function babelUnSerialize(string $input): ? BabelSerializable
    {
        $data = json_decode($input, true);
        if (!is_array($data)) {
            return null;
        }

        return new static(
            $data['text'] ?? '',
            $data['level'] ?? TextMsg::INFO,
            $data['createdAt'] ?? microtime(true)
        );
    }

    public function babelSerialize(): string
    {
        return json_encode($this->getData(), JSON_UNESCAPED_UNICODE);
    }

    public function getData(): array
    {
        return [
            'text' => $this->text,
            'level' => $this->level,
            'createdAt' => $this->getCreatedAt()
        ];
    }

    public function getText(): string
    {
        return $this->getText();
    }


    public function getTrimmedText(): string
    {
        return  StringUtils::trim(StringUtils::sbc2dbc($this->text));
    }

    public function getLevel(): string
    {
        return $this->level;
    }


}