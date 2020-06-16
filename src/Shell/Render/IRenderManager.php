<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Render;

use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Shell\Render\Renderer;
use Commune\Blueprint\Shell\Render\RendererOption;
use Commune\Blueprint\Shell\Render\RenderManager;
use Commune\Protocals\Intercom\OutputMsg;
use Commune\Support\Protocal\ProtocalMatcher;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRenderManager implements RenderManager
{
    /**
     * @var RendererOption[]
     */
    protected $options = [];

    /**
     * IRenderManager constructor.
     * @param RendererOption[] $options
     */
    public function __construct(array $options)
    {
        foreach ($options as $option) {
            $this->register($option);
        }
    }

    public function register(RendererOption $option): void
    {
        $id = $option->getId();
        $this->options[$id] = $option;
    }

    /**
     * @return RendererOption[]
     */
    public function getOptionMap(): array
    {
        return $this->options;
    }

    public function getRenderer(ReqContainer $container, string $id, array $params = []): Renderer
    {
        $option = $this->getOptionMap()[$id] ?? null;

        if (isset($option)) {
            return $container->make($option->renderer, $params);
        }

        return $container->make($id, $params);
    }


    public function render(
        ReqContainer $container,
        OutputMsg $output,
        ProtocalMatcher $matcher
    ): array
    {
        $hostMsg = $output->getMessage();

        // 要遍历各种策略, 找到第一个独占的策略.
        $gen = $matcher->matchEach($hostMsg);
        foreach ($gen as $protocalHandlerOpt) {

            $renderer = $this->getRenderer(
                $container,
                $protocalHandlerOpt->handler,
                $protocalHandlerOpt->params
            );

            $rendered = $renderer($hostMsg);

            // 为 null 表示要继续寻找其它策略渲染.
            if (isset($rendered)) {
                // 空数组表示这个消息要被取消掉.
                return empty($rendered)
                    ? []
                    : $output->derive(...$rendered);
            }
        }

        // 如果什么都没处理, 则原样返回.
        return [$output];
    }


}