<?php

namespace Commune\Message\Host\Convo\Media;


use Commune\Protocals\HostMsg;
use Commune\Protocals\HostMsg\Convo\Media\VideoMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Support\Utils\StringUtils;

/**
 * BiliBili 网站的视频消息.
 * 本来这种差异化的消息应该作为 shell 端的渲染结果使用.
 * 不应该当成原生消息来发布.
 *
 * 不过... 我现在没有时间细细开发整套.
 * 正规的做法要在渲染端有 option registry, resource registry
 * 从 resource 中加载到 runtime
 * 然后再在 shell 端做 biliVideoRenderer
 *
 * 工作量还要有好几个小时才行. 还得测试...
 *
 * 考虑到这是自己开发的项目, 而 b 站视频是最初用到 demo 里的视频.
 * 就先给自己一个特权, 先把 bili 当成一种直接消息来使用吧.
 *
 * @property string $iframe
 * @property int $start
 * @property string $text
 * @property string $level
 */
class BiliVideoMsg extends AbsMessage implements VideoMsg
{
    protected $_resource;

    protected $_text;

    public static function instance(
        string $iframe,
        string $text = '',
        int $start = 0
    ) : self
    {
        return new static([
            'iframe' => $iframe,
            'start' => $start,
            'text' => $text,
        ]);
    }

    public static function stub(): array
    {
        return [
            'iframe' => '',
            'start' => 0,
            'text' => '',
            'level' => HostMsg::INFO
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function getProtocalId(): string
    {
        return $this->getResource();
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getResource(): string
    {
        if (isset($this->_resource)) {
            return $this->_resource;
        }

        $resource = StringUtils::fetchSrcFromDom($this->iframe);
        return $this->_resource = $resource ?? '';
    }

    public function getText(): string
    {
        if (isset($this->_text)) {
            return $this->_text;
        }


        $text = $this->text;
        $resource = $this->getResource();

        $id = null;
        if (!empty($resource)) {
            $parts = explode('?', $resource, 2);
            $part1 = $parts[1] ?? '';

            if (!empty($part1)) {
                parse_str($part1, $parsed);
                $id = $parsed['bvid'] ?? null;
            }
        }

        if (isset($id)) {
            $text = $text . " https://www.bilibili.com/video/$id/";
        }

        return $this->_text = $text;
    }

    public function isEmpty(): bool
    {
        $resource = $this->getResource();
        return empty($resource);
    }


}