<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IMindDef;

use Commune\Blueprint\Ghost\MindDef\ChatDef;
use Commune\Blueprint\Ghost\MindMeta\ChatMeta;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IChatDef implements ChatDef
{
    /**
     * @var ChatMeta
     */
    protected $meta;

    public function __construct(
        string $say,
        string $reply,
        string $index = '',
        string $cid = null
    )
    {
        $cid = $cid ?? md5($say);
        $rCid = "$index:$cid";
        $this->meta = new ChatMeta([
            'cid' => $rCid,
            'say' => $say,
            'reply' => $reply,
            'index' => $index,
        ]);
    }

    /**
     * @return ChatMeta
     */
    public function toMeta(): Meta
    {
        return $this->meta;
    }

    /**
     * @param ChatMeta $meta
     * @return Wrapper
     */
    public static function wrapMeta(Meta $meta): Wrapper
    {
        return new static(
            $meta->say,
            $meta->reply,
            $meta->index,
            $meta->cid
        );
    }

    public function getIndex(): string
    {
        return $this->meta->index;
    }

    public function getCid(): string
    {
        return $this->meta->cid;
    }

    public function getSay(): string
    {
        return $this->meta->say;
    }

    public function getReply(): string
    {
        return $this->meta->reply;
    }

    public function getName(): string
    {
        return $this->meta->cid;
    }

    public function getTitle(): string
    {
        return $this->meta->say;
    }

    public function getDescription(): string
    {
        return $this->meta->say;
    }

}