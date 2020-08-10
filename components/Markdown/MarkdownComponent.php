<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown;

use Commune\Blueprint\Framework\App;
use Commune\Ghost\Component\AGhostComponent;


/**
 * 核心组件! 用 markdown 文档来撰写多轮对话
 * 这个组件基本完成, chatlog 项目就初步齐活了.
 *
 * 目标是可以自动解析 markdown 文档, 得到树状结构的数据.
 * 然后把树状结构的数据使用指定的 Parser 解析为 TreeContext
 *
 * 在 TreeContext 里使用指定的 markdownStrategy 管理上下文逻辑.
 *
 * 用 @comment 注解的形式来定义其中的对话逻辑细节. 包括视频对话.
 * php 8.0 会实装 Attribute 功能, 现阶段先称注解的做法为 Annotation.
 *
 * 希望这个功能能够成功!! 我只有不到两天时间来开发这个功能.  ~2020-08-09
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 *
 *
 *
 *
 *
 */
class MarkdownComponent extends AGhostComponent
{
    public static function stub(): array
    {
        return [];
    }

    public static function relations(): array
    {
        return [];
    }

    public function bootstrap(App $app): void
    {
    }


}