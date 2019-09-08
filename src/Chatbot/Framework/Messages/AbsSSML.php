<?php


namespace Commune\Chatbot\Framework\Messages;

use Commune\Chatbot\Blueprint\Message\SSML;

abstract class AbsSSML extends Reply implements SSML
{
    /**
     * @var string
     */
    protected $content;

    protected $attributes = [];

    /**
     * @var SSML[]
     */
    protected $subSsmls = [];

    /**
     * AbsSSML constructor.
     * @param string $content
     * @param array $attributes
     * @param SSML[] $subSsmls
     */
    public function __construct(
        string $content,
        array $attributes = [],
        array $subSsmls = []
    )
    {
        $this->content = $content;
        $this->attributes = $attributes;
        $this->subSsmls = $subSsmls;
        parent::__construct(static::class);
    }


    public function isEmpty(): bool
    {
        return false;
    }

    public function getText(): string
    {
        $content = $this->getContent();
        foreach ($this->subSsmls as $id => $ssml) {
            $content = str_replace(
                static::TAG_L . $id . static::TAG_R,
                $ssml->getText(),
                $content
            );
        }

        return $content;
    }

    public function getFormatted(): string
    {
        $content = $this->getContent();
        $tag = $this->getTag();

        foreach ($this->subSsmls as $id => $ssml) {
            $content = str_replace(
                static::TAG_L . $id . static::TAG_R,
                $ssml->getText(),
                $content
            );
        }

        if (empty($tag)) {
            return $content;
        }

        $attr = '';
        foreach ($this->attributes as $name => $value) {
            $attr.= " $name=\"$value\" ";
        }
        return "<$tag$attr>$content</$tag>";
    }

    public function toMessageData(): array
    {
        return [
            'content' => $this->getContent(),
            'attrs' => $this->getAttrs(),
            'subSSMLs' => array_map(function(SSML $ssml) {
                return $ssml->toArray();
            }, $this->subSsmls)

        ];
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getAttrs(): array
    {
        return $this->attributes;
    }

    public function getAttr(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function setAttr(string $name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function getSubSSMLs(): array
    {
        return $this->subSsmls;
    }


    public function namesAsDependency(): array
    {
        $names = parent::namesAsDependency();
        $names[] = SSML::class;
        return $names;
    }


}