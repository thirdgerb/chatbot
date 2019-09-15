## 2019-09-15

-   Option 的 validate 方法改为静态方法.
-   ComponentOption 增加了LoadEvent, LoadRenderer 与 LoadMemoryBag三个方法.
-   Reply 增加了 withSlots().  Collection 类的所有方法返回的都是一个新的Collection. 这样原Collection 数据不会改动.
-   修复了由于 Collection->merge 结果并非引用导致的一处bug. 类似情况观察中.


