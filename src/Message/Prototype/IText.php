<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Prototype;

use Commune\Message\Blueprint\TextMsg;
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
     * @var string
     */
    protected $trimmed;

    /**
     * IText constructor.
     * @param string $text
     * @param string $level
     * @param float|null $createdAt
     */
    public function __construct(string $text = '', string $level = TextMsg::INFO, float $createdAt = null)
    {
        $this->text = $text;
        $this->level = $level;
        parent::__construct($createdAt);
    }

    public function __sleep(): array
    {
        return [
            'text',
            'level',
            'createdAt'
        ];
    }

    public function isEmpty(): bool
    {
        $text = $this->getTrimmedText();
        return $text === '';
    }


    public function getText(): string
    {
        return $this->getText();
    }


    public function getTrimmedText(): string
    {
        return $this->trimmed
            ?? $this->trimmed = StringUtils::trim(StringUtils::sbc2dbc($this->text));
    }

    public function getLevel(): string
    {
        return $this->level;
    }


}