<?php


namespace Commune\Chatbot\Framework\Messages;


use Carbon\Carbon;
use Commune\Chatbot\Blueprint\Message\MediaMsg;

abstract class AbsMedia extends AbsConvoMsg implements MediaMsg
{
    /**
     * @var string
     */
    protected $url = '';

    public function __construct(string $url, Carbon $createdAt = null)
    {
        $this->url = $url;
        parent::__construct($createdAt);
    }

    public function __sleep(): array
    {
        return array_merge(parent::__sleep(), ['url']);
    }

    public function getUrl(): string
    {
        return $this->url;
    }


    public function isEmpty(): bool
    {
        return null;
    }

    public function getText(): string
    {
        return $this->toJson();
    }

}