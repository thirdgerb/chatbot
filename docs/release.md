## 2019-09-15

-   Option 的 validate 方法改为静态方法.
-   ComponentOption 增加了LoadEvent, LoadRenderer 与 LoadMemoryBag三个方法.
-   Reply 增加了 withSlots().  Collection 类的所有方法返回的都是一个新的Collection. 这样原Collection 数据不会改动.
-   修复了由于 Collection->merge 结果并非引用导致的一处bug. 类似情况观察中.

-   修复了 Context 的 Entity 依赖记忆情况下的 bug
-   ContextDefinition 的startStage, 现在检查 Entity 变成可选步骤了.
-   重要: 修改了 dependOn 的逻辑, 现在允许指定目标 context 的 stages. 在一些将Context 当成依赖数据的场景下好用.
-   tracking 日志在 hostConfig 配置里加了开关.


