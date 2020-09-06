
# 对话式 wiki

[//]: # (@stageName intro)

对话式 wiki 也是作者对与对话交互系统的核心设想之一。简单来说，这个对话系统将成为个人进行创作和知识查询的核心入口，它包含知识创作、管理、查询三个方面的技术思路。

[//]: # (@askChoose)
[//]: # (@routeToRelation children)
[//]: # (@routeToStage ending)


## 基于 markdown 的结构化知识创作

[//]: # (@stageName markdown_wiki)

Markdown 是目前最为常见的 wiki 编写语言。本项目也把 markdown 的多轮对话编写当成了技术探索的第一步。

[//]: # (@info)

您现在与之对话的这个机器人，就是作者用 Markdown 的形式编写的。
它与编写一篇文章非常接近，只是可以进一步地使用```[//]: # (@break)``` 这样的注解来教机器人更复杂的对话行为。

[//]: # (@break)

所谓 "半开放域" 对话场景，最大的特点就是核心内容由服务端来主导，伴随开放性的长尾对话 （例如问答、评论）。
而服务端主导的对话流程，绝大多数都可以用 "树" 来描述。因此 markdown 就成为了绝佳的对话机器人书写形式。

[//]: # (@info)

基于对 Markdown 树形结构的解析，我们可以设置多轮对话的默认流向，可以是线性的（例如当前的对话, 相当于正序遍历），也可以是分枝式的，也可以是自定义更复杂的规则。

[//]: # (@break)

进一步的，其实现在网络上绝大多数的知识（文章）都用类似 markdown 这样结构严谨的文档来编写。
我可以把某个技术项目的说明文档（vue，swoole等），或者类似 [JavaGuide](https://github.com/Snailclimb/JavaGuide) 这样的技术笔记直接转化为多轮对话。

[//]: # (@info)

再结合 全文搜索 + 自然语言识别 的技术让用户通过对话快速定位到知识节点上。
并用一个可以逐步教学成长的 问答/闲聊 系统来承担长尾的应答能力。
这样就是一个完整的半开放域解决方案了。


[//]: # (@askChoose)
[//]: # (@routeToRelation brothers)
[//]: # (@routeToRelation parent b|返回)


## 面向对象的对话式知识设计

[//]: # (@stageName oop_dialogic_knowledge)

对话式 Wiki 的本质是可交互 Wiki，它的核心是面向对象思想。

[//]: # (@info)

简单而言，我们把一个知识点 "封装" 成一个面向对象的数据结构 （Object），这个数据结构和编程语言的数据结构并无本质区别。

[//]: # (@break)

它对外的呈现，无论是对话形式，还是可视化的形式，都属于 "View" 这个层面的差别。一个知识结构可以有若干种预先定义的 "View"。

[//]: # (@info)

进一步的，所有知识点的 Object，可以通过 Object Class 预定义各种 Relation（关联关系），Relation 既可以让我们从一个知识点跳跃到另一个知识点，也可以将另一个知识点用固定的 "Link View" 展示到当前知识里。


[//]: # (@info)

而围绕着知识点可以有各种各样的操作方法，包括常见的增删改查，或更复杂的人机交互。这些操作只可能是用工程手段严格定义的。它们和面向对象原理一样，也存在着作用域、参数等各种问题。

[//]: # (@break)

最后，知识的呈现、关联关系、调用方法通过对话的方式去驱动。从而实现对话式的面向想对象知识管理。这是作者在对话系统知识管理上的核心目标。


[//]: # (@askChoose)
[//]: # (@routeToRelation brothers)
[//]: # (@routeToRelation parent b|返回)


## 知识图谱技术的应用

[//]: # (@stageName knowledge_graph)

很明显，CommuneChatbot 项目在知识管理上的思路，和知识图谱技术有很大的相似性。事实上作者 15年、16年反复写了很多个版本的数据表结构定义，试图用关系型数据库定义出知识图谱式的数据结构来。由于技术能力的不足，无法达到预期的目的。

[//]: # (@info)

当前版本因为时间和精力有限，还没有引入知识图谱数据库。但所有的核心配置、数据存储，都初步考虑了与知识图谱的结合。CommuneChatbot 项目的各种数据都是通过 Option 对象驱动的，理想情况下每个 Option 都可以自解释地直接转为知识图谱对象。

[//]: # (@break)

但我也要指出两种思路最本质的区别。目前自然语言理解技术，把知识图谱当成承载人工智能理解结果的对象。

[//]: # (@info)

它反映的主要是算法分析的结果。而我则是把它当成人类知识管理的工具，数据的生产、关联都主要取决于人类的创作。

[//]: # (@info)

所以我最主要的考虑是，如何把人类用编程语言规范描述的 Class，自动转为知识图谱的数据结构，并且根据注释等条件自动生成相关的多轮对话。

[//]: # (@askChoose)
[//]: # (@routeToRelation brothers)
[//]: # (@routeToRelation parent b|返回)


## CommuneChatbot 目前的探索

[//]: # (@stageName commune_current_explore)

简单来说，CommuneChatbot 还没进入到知识管理功能的开发阶段，但准备了若干个技术实现：

[//]: # (@info)

配置中心抽象层：项目所用到的数据都通过各种结构化的 Option 进行描述，而不同 Option 之间可以像 ORM 一样建立起 Relation。这样的数据结构理论上可以直接映射为知识图谱的数据。因此我并不是直接用关系型数据库存储它们，而是建立了一个抽象层，未来只要替换掉 StorageDriver，任何一种 Option 都可以随意使用关系型数据库、文件、NoSql 数据库、知识图谱去存取。

[//]: # (@break)

面向对象自解释：CommuneChatbot 花了很大的精力，设计了一种将面向对象语言自动转化为多轮对话的策略。简单而言，可以设计一个 Class，定义它的 Property 和 Method，而这些 Property 与 Method 会自动转化为多轮对话的相关能力。举一个例子

[//]: # (@info)

```php

/**
 * 教机器人怎么做. 理论上只允许管理员操作.
 *
 * @title 教对话机器人基本回复策略.
 *
 * @property-read string $batchId  教学任务的 id
 *
 * @property string|null $selectedIntent    已经选择的意图
 * @property string[] $intentChoices        可供选择的意图
 * @property string|null $createIntent      准备创建的意图
 * @property string|null $strategyScope     回复策略的作用域
 *
 * # getters
 * @property-read FallbackSceneOption|null $scene   产生教学任务的场景
 */
class LesionTask extends ACodeContext
{

    /**
     * @title 入口校验.
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_start(StageBuilder $stage): StageBuilder

    /**
     * @title 任务场景概述
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_brief(StageBuilder $stage) : StageBuilder

    /**
     * @title 选择菜单
     * @spell #menu
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_menu(StageBuilder $stage) : StageBuilder

    /*-------- skip ---------*/

    /**
     * @title 跳过这个任务
     * @desc 暂时跳过
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_skip(StageBuilder $stage) : StageBuilder

    /**
     * @title 忽略
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_ignore(StageBuilder $stage) : StageBuilder

    /**
     * @title 人工回复
     * @desc 人工回复 (不学习)
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_reply(StageBuilder $stage) : StageBuilder

    /*-------- 闲聊定位 ---------*/

    /**
     * @title 使用闲聊
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_chat(StageBuilder $stage) : StageBuilder

    /*-------- 退出对话 ---------*/

    /**
     * @title 完成教学.
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_learned(StageBuilder $stage) : StageBuilder


    /**
     * @title 完成任务.
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_done(StageBuilder $stage) : StageBuilder


    /*-------- 异常退出 ---------*/

    /**
     * @title 主动退出教学
     * @param StageBuilder $stage
     * @return StageBuilder
     * @spell 退出
     */
    public function __on_cancel(StageBuilder $stage) : StageBuilder

    /**
     * @title 主动退出完整对话
     * @param StageBuilder $stage
     * @return StageBuilder
     * @spell 退出
     */
    public function __on_quit(StageBuilder $stage) : StageBuilder


}
```

[//]: # (@info)

这个类可以通过 "注释" 的方式，经由解析器自动生成多轮对话的上下文节点，并且通过注释定义语料、意图匹配策略等。作者预期未来的知识图谱的类型定义，可以用这种面向对象的方式去实现。


[//]: # (@askChoose)
[//]: # (@routeToRelation parent b|返回)
[//]: # (@routeToStage ending)

# 结束

[//]: # (@stageName ending)
[//]: # (@goFulfill)

