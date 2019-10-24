<?php


namespace Commune\Chatbot\App\Messages\Replies;


use Commune\Chatbot\App\Messages\ReplyIds;
use Commune\Chatbot\Blueprint\Message\Replies\LinkMsg;
use Commune\Chatbot\Blueprint\Message\Tags\NoTranslate;
use Commune\Chatbot\Framework\Messages\AbsVerbose;
use Illuminate\Support\Collection;

/**
 * 可以用参数, 渲染出一个超级链接.
 * 通过 translator 来做模板.
 */
class Link extends AbsVerbose implements LinkMsg, NoTranslate
{
    /**
     * @var Collection
     */
    protected $_slots;

    /**
     * @var string
     */
    protected $_url;

    /**
     * @var string
     */
    protected $_title;

    public function __construct(string $url, string $title = null)
    {
        $this->_url = $url;
        $this->_title = $title ?? $url;
        $this->_slots = new Collection([
            'url' => $this->_url,
            'title' => $this->_title
        ]);

        parent::__construct('');
    }

    public function __sleep(): array
    {
        return array_merge(parent::__sleep(), ['_url', '_title', '_slots']);
    }

    public function getReplyId(): string
    {
        return ReplyIds::LINK;
    }

    public function getSlots(): Collection
    {
        return $this->_slots
            ?? $this->_slots = new Collection();
    }

    public function getUrl(): string
    {
        return $this->_url;
    }

    public function mergeSlots(array $slots): void
    {
        $this->_slots = $this->getSlots()->merge($slots);
    }

    public function getText(): string
    {
        return $this->_title;
    }

    public static function mock()
    {
        return new static( 'https://communechatbot.com', 'CommuneChatbot');
    }

}