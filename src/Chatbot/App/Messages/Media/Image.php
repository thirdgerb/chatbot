<?php


namespace Commune\Chatbot\App\Messages\Media;

use Commune\Chatbot\Framework\Messages\AbsMedia;
use Commune\Chatbot\Blueprint\Message\Media\ImageMsg;

class Image extends AbsMedia implements ImageMsg
{
    /**
     * 图片的地址
     *
     * @var string
     */
    protected $url;


    /**
     * Image constructor.
     * @param string $id
     * @param string $url
     */
    public function __construct(string $id, string $url = '')
    {
        $this->url = $url;
        parent::__construct($id);
    }


    public function getUrl(): string
    {
        return $this->url;
    }

    public function getText(): string
    {
        return "image:$this->url";
    }

    public function toMessageData(): array
    {
        return [
            'url' => $this->url,
            'mediaId' => $this->getMediaId(),
        ];
    }

    public function namesAsDependency(): array
    {
        $names = parent::namesAsDependency();
        $names[] = ImageMsg::class;
        $names[] = self::class;
        return $names;
    }

}