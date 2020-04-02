<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Prototype\Media;

use Commune\Message\Blueprint\MediaMsg;
use Commune\Message\Prototype\AMessage;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AMedia extends AMessage implements MediaMsg
{
    /**
     * @var string
     */
    protected $resource;

    public function __construct(string $resource = '', float $createdAt = null)
    {
        $this->resource = $resource;
        parent::__construct($createdAt);
    }

    public function __sleep(): array
    {
        return [
            'resource',
            'createdAt',
        ];
    }

    public function isEmpty(): bool
    {
        return $this->resource;
    }

    public function getResource(): string
    {
        return $this->resource;
    }


}