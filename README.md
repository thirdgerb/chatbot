

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


### 2019年2月17日

-   周末对引擎进行了大规模重构完善, 重构了近5000行代码. 引擎的功能基本能让自己满意.
-   下一阶段计划
    1.  在laravel 里作为组件实装, 立刻让命令行生效.
    2.  完成 "记日记" 的app
    3.  微信内部实装.
-   下下步计划
    -   实现 "知识库" app, 在微信中可用.
-   时间预期
    -   3月间

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


