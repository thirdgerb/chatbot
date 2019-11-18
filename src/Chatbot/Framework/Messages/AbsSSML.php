<?php


namespace Commune\Chatbot\Framework\Messages;

use Commune\Chatbot\Blueprint\Message\Replies\SSML;

/**
 * ssml 的一个封装尝试.
 * 用此类搭建一个树状的 ssml 结构.
 */
abstract class AbsSSML extends AbsReply implements SSML
{
    /**
     * 当前 xml 对象的模板.
     * @var string
     */
    protected $content;

    /**
     * 当前 xml 对象的属性.
     * @var array
     */
    protected $attrs = [];

    /**
     * 当前 xml 对象的子元素. 子元素渲染后会用 {key} 模板渲染进去.
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
        $this->attrs = $attributes;
        $this->subSsmls = $subSsmls;
        parent::__construct(static::class);
    }

    public function __sleep() : array
    {
        return array_merge(parent::__sleep(), [
            'content',
            'attrs',
            'subSsmls',
        ]);
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
        foreach ($this->attrs as $name => $value) {
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
        return $this->attrs;
    }

    public function getAttr(string $name)
    {
        return $this->attrs[$name] ?? null;
    }

    public function setAttr(string $name, $value)
    {
        $this->attrs[$name] = $value;
    }

    public function getSubSSMLs(): array
    {
        return $this->subSsmls;
    }


}