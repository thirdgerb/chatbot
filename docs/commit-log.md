## 2019-10-07

-   service provider 现在默认有 IS_PROCESS_SERVICE_PROVIDER 属性, 并且会在启动时检查.
-   增加了 Paragraph 功能, 允许将多个 reply 合并成一个段落, 对某些分端发消息有用处.
-   Analyser 现在只在匹配了命令之后, 才查看用户权限.
-   dialog->restart 会重新对当前context 的 Entity 进行校验.
-   Chat 又去掉了 scene, 所有的 scene 共享会话内容. 区分 scene 时要在端上用 #home 和 #quit
-   ChatPipe 现在一些校验失败的情况不再记录日志
-   增加了 conversational 类型的 message tag, 方便有些端做对应功能.
-   优化了 selfTranslate 类型的 message tag.
-   优化了 questionTemp 渲染 suggestions 时进行的翻译.
-   简化了 session logger, 不要用依赖注入了.


