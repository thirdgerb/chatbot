

开发中.

可以执行 ``` php demo/test.php ``` 做测试.


## 目标feature

-   基础
    -   所有IM平台通用, 对输入输出进行统一抽象化
    -   可分布式部署
    -   不依赖单进程模型, 可以在swoole, workman或 roadrunner 中运行.
    -   demo 目标支持 命令行 + wechat
-   应用
    -   分布式响应时, 不发生逻辑冲突
    -   完全记录所有消息
    -   响应消息通过可拓展的管道机制
    -   支持NLP单元作为中间件
    -   webhook
    -   完整的语境系统
        -   目前方案采用命令式响应
        -   语境的路由层完全可配置 (不依赖代码实现应用逻辑)
        -   有状态的上下文, 允许各种场景转移
        -   面向 scope 的语境记忆
        -   支持 开放域 / 有限域 / 封闭域 响应
    -   兼容botman 式api
    -   支持基本的对话模式
        -   ask
        -   choose
        -   confirm


## 开发计划

### 2019年2月19日

刚想到的, 记录一下.

-   改动
    -   考虑表单提交的场景, intended 应该能通过 /back /forward 来移动, 并重新提交.
    -   如果intended 要可以移动, 那需要修改guest和intend 机制.
    -   cancel 应该起到跳出当前对话的作用, 而不是和 backward 一样.
    -   cancel 应该是 DialogRoute 的默认节点, 允许进行定义, 比如跳转.
    -   fail 也应该是 DialogRoute 的默认节点.
-   需要思考的问题
    -   对话引擎如果要跨平台进行身份认证, 需要有统一的方案.
    -   对话过程中, 如果要输入密码之类的加密信息, 就不太合适. 需要有别的解决办法.
    -   富文本对话里是有 "卡片" 的, 需要把和卡片的交互, 与语境结合起来.
    -   增加一些 predefined 的对话设计. 例如 loop, 降低工程量.
-   应用设计方案
    -   日记功能.   把对话界面当成记事本.
    -   写文章.  基于IM自有的 文章 + 图片 系统, 在对话中生成文章. 可能 Message 要考虑一些app, 设计成带attachments的形式.
    -   知识库. 基于IM命令, 操作一个类似wiki 的树状知识库. 允许公共编辑.
    -   问答小程序. 通用的问答程序, 可在线配置答案, 通过选项式的交互界面来挑选结果. 希望这种方式速度更快.
    -   问卷调查. 自动开始提问, 回答则是问题的答案. 最后确认可提交.
    -   流程管控.  把对话定义成固定的流程, 用户按流程的约束来输入.... 作为最核心的一种应用形式.
    -   接口调试工具.

[开发计划](docs/plan.md)

## 更新内容


### 20190217 v1 更新

大规模更新. 改动非常大.

-   重构了Intent. 现在用命令式的策略重构了Intent, 包括
    -   使用Laravel command 式的 signature 来定义Intent
    -   基于symfony inputDefinition 来定义 Intent 的参数, 同时也定义了name, default, description 等.
    -   梳理了IntentId 的意涵, 进行了规范.
    -   修改了Intent match的整个流程, 对命令式的输入做了特殊对待.
    -   Intent 命中却参数不充分的情况, 现在可以自动提问, 开始fulfill 参数的过程. 这个很有用.
    -   允许一个route action 判断intent的参数是否符合标准, 不符合可以按照自己的定义要求用户重新输入. 表单式的功能还需要进一步完善.
    -   Context 在回调时自动转为 Intent
    -   完善了 IntentFactory 的各种matcher, 需要在实践中进一步完善. regex 目前还没做.
-   重构了IntentRoute 的action, redirect 等处的api, 使之更严谨, IDE识别更好. 不用数组, 而用树来实现.
-   优化了之前Guest 和Intended 存在的混乱逻辑. 现在回调方法由guest 的那个路由来确定, 这样不会影响Intended 的本意.
-   拆分了userCommandPipe 和 analyzerPipe, 为管理者和用户提供两套命令.
-   按需重构了Director, 增加了部分Runner, 方便 action 中间制定跳转路径. 不推荐的做法.
-   新增了多条命令, 方便调试.
-   按需补充了个别单测.
-   加了一个时间检查单元, 一个对话纯逻辑只需要 几百 us 就可以完成.
-   优化了多个对象字符串输出方式, 默认支持 toJson
-   优化了部分异常处理.
-   增加了默认的 bootstrapper, preload 各种config.


[更新内容](docs/release.md)


