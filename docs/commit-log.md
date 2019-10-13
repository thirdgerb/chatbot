## 2019-10-13

-   messages 大规模重构
    -   app messages 目录结构做了相关调整. 在最后关头争取一个比较合理的结果.
    -   定义了通用的 namesAsDependency 方法, 不用再一个个定义了.
    -   统一了 message 的 __sleep 和 toMessageData() 的关系.
    -   增加了 Link 类型的 message, 它本身是 reply, 允许客户端做专门的渲染.
    -   去掉了 isMessageType 的方法和概念, 用 instanceof 判断更靠谱.
    -   建立了 message 的测试体系
        -   普通消息继承自 AbsConvoMsg, 和 Context 区分.
        -   AbsConvoMsg 要求实现 mock 方法, 方便测试.
        -   为 messages 添加了通用的测试用例, 可以测试 message 定义是否完整.
    -   重做了 paragraph
    -   重做了 conversation::render 流程. 目前仍然不够简洁.

-   增加了 logDialogStatus 机制. 允许在 dialog wait 的时候, 在request 里记录 dialog 的数据.

-   Speech 类改动
    -   dialog speech 增加了 withReply 方法, 方便与 paragraph 结合使用.
    -   去掉了 info 等方法的 string 类型约束, 方便方法调用... 这种场景强类型反而麻烦.

-   其它
    -   ArrayUtils 增加了 recursiveToArray 方法
    -   优化了Menu
    -   Demo 多轮对话小改动.
