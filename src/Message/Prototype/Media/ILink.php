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

use Commune\Message\Blueprint\Media\LinkMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ILink extends AMedia implements LinkMsg
{
    /**
     * @var string
     */
    protected $title;

    public function __construct(string $resource = '', string $title = null, float $createdAt = null)
    {
        $this->title = $title ?? $resource;
        parent::__construct($resource, $createdAt);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function __sleep(): array
    {
        return array_merge(parent::__sleep(), ['title']);
    }


}