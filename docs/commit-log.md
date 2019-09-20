## 2019-09-20

-   memory 现在的construct 不在定义为 final. 从而允许用户自己生产更具多样性的ID. 风险是如果不能 new 出来, 有可能造成致命错误.
-   buildTalk 现在允许传入 slots
-   Dialog 增加了 belongsTo, isDependedBy, isDepended 三个方法. 用于判断 dialog 所在的上下文.
-   Dialog 增加了 findContext, 可以通过contextId 去获取. 在特殊的情况下可能有用.
-   Redirector 这种 navigator 现在允许传入目标context 的stage, 从而不是start 而是直接进入该stage.
    -   这是一种接近hack 的办法, 以后要找更好的解决方案.
-   replaceTo 增加了上述 stage 参数.
-   context 的 name 现在大小写无关.
-   Schematic\Entries 增加了 ArrayAccess


