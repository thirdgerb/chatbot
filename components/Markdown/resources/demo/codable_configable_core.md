
# 完全可编程与完全可配置的多轮对话内核

CommuneChatbot 项目的多轮对话内核不仅实现了很多复杂的对话特性，在工程上的实现也是非常费神的。

它定义了 "完全可编程" 和 "完全可配置" 两个必须要实现的特性，最终形成了 编程-配置-编程/配置 的三明治架构。


## 可编程与可配置的区别

现阶段需要工程手段定义的多轮对话系统，通常有两种做法。

- 代码驱动：所有对话逻辑都通过代码编写。
- 配置驱动：所有对话逻辑通过约定结构的配置来定义。

许多朋友似乎认为写配置比写代码更高级，但作者在反复实践中确定了以下道理：

- 代码驱动和配置驱动本质上是一回事，配置是代码能力的子集
- 完全可编程的优势在于最自由地调度所有对话能力，清晰、可解释、方便调试
- 配置驱动的优势在于特定类型多轮对话下很简洁，并且可以批量导入

配置就是高级封装的代码，代码就是低级封装的配置。一旦逻辑细节要求复杂时，配置会变得无比复杂和难以阅读，所以配置并不比编程高级，而是一种辅助工具。

对于一个对话系统而言，完全可编程的能力是第一位的。在此基础上，可以封装出各种各样规则的配置，这个配置也可以用图形界面去拖拽，也可以用解析器分析导入（对 markdown 导入），也可以在对话中生成。

这些能力对于非技术的运营人员非常惹眼，但开发者应该明白他们本质是一样的。

## 对话逻辑配置的可平移性

理想的多轮对话配置是高度抽象的，不要耦合任何底层逻辑代码。

这样的多轮对话是可平移的，比如我有信心把任何 DuerOS 或 DialogFlow 里的多轮对话配置平移到本项目，并且完全兼容。这是建立在本项目的底层多轮对话能力必须比它们更完善的前提下。

## 编程-配置-编程/配置 的三明治架构

由于 CommuneChatbot 一直在探索 "对话式编程" 的可能性，因此必须实现 "双层解释器" 的思路。

简单而言，用户在多轮对话交互中试图教会机器人新的能力，机器人要将理解转化为可保存、可读取、可修改的配置。这是第一层解释器的任务。

而这些生成的中间配置被系统读取、加载，驱动多轮对话内核本身，于是就掌握了新的对话能力。基于配置驱动的内核相当于第二层解释器。这就需要一个完全配置驱动层。

由于系统要求底层有完全可编程的能力，所以对话管理内核最底层是完全用代码来驱动的。于是整体形成了三层架构。

- 最底层直接使用编程语言，管理通讯、调度对话逻辑、修改对话状态数据，完全可编程
- 中间层是配置层，所有底层的代码都强制封装成中间层的配置，而对话管理内核只通过读取配置，才能调用底层方法
- 最高层则可以是代码层，也可以是配置层。可以按需求做各种高级封装，怎么方便怎么来。

## 三明治架构的配置式 Demo

您现在看到的多轮对话，就是一个典型的配置式 Demo。


对话的源码是 [Markdown 文件](https://github.com/thirdgerb/chatbot/blob/master/components/Markdown/resources/demo/commune_v2_intro.md)，可以视作一种高级配置。

这种高级配置通过专门的 [解析器模块](https://github.com/thirdgerb/chatbot/tree/master/components/Markdown/Parsers)，解析为中间层的配置。

中间层的配置对象，[SectionStageDef](https://github.com/thirdgerb/chatbot/blob/master/components/Markdown/Mindset/SectionStageDef.php) 保存在配置中心抽象层，系统会动态加载，可以设置内存缓存（不设缓存则每次调用逻辑都会查询）。

而底层的完全可编程驱动，则是通过[SectionStrategy](https://github.com/thirdgerb/chatbot/blob/master/components/Markdown/DefStrategy/SectionStrategy.php) 来实现的。


## 三明治架构的代码式 Demo

项目的对话教学任务完全是用代码编写的，因为逻辑极其复杂，涉及各种数据查询和校验，因此几乎不可能通过配置来实现。

因此逻辑完全用一个面向对象的类 [LesionTask](https://github.com/thirdgerb/chatbot/blob/master/components/HeedFallback/Context/LesionTask.php) 来书写。

这个类像面向对象一样定义了一个多轮对话，包含属性与各种可用的方法，而这些方法被自动解析为多轮对话的节点。

解析出来的中间结果，用类似 [StageMeta](https://github.com/thirdgerb/chatbot/blob/master/src/Blueprint/Ghost/MindMeta/StageMeta.php) 的数据格式保存在配置中心抽象层。

而底层驱动逻辑的代码，则定义在[ContextDef](https://github.com/thirdgerb/chatbot/blob/master/src/Ghost/Context/Codable/ICodeContextDef.php) 这样的基类中。

