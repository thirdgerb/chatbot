## 2019-10-28


-   开发了模拟 疑案追声 的文字游戏, 用于展示双方的可能性.
    -   纯声音游戏可以做到智能音箱中
    -   CommuneChatbot 可以在这个方向做尝试.
-   Option 改动
    -   entries 的 iterable 机制在foreach嵌套的情况下会有问题, 将之简化了.
-   Story
    -   用 tag 可以正确搜索出相关的 def 了
-   host
    -   fulfill 又加回了 skipSelfExitingEvent 参数. 允许被自身拦截.
    -   beginParagraph 增加了一个参数 $joint, 可以决定多个消息合并的方式.
    -   memory 不再定义 final 方法, 让开发者自己想合适的策略吧.
    -   askContinue 不再用 confirm, 而是用 askVerbose


