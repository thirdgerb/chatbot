<?php


namespace Commune\Chatbot\Framework\Messages\Media;

use Commune\Chatbot\Framework\Messages\AbsMedia;
use Commune\Chatbot\Blueprint\Message\Media\Image as ImageItf;

class Image extends AbsMedia implements ImageItf
{
    /**
     * @var string
     */
    protected $url;

    /**
     * Image constructor.
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
        parent::__construct();
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
        ];
    }

    public function namesAsDependency(): array
    {
        $names = parent::namesAsDependency();
        $names[] = ImageItf::class;
        $names[] = self::class;
        return $names;
    }

}