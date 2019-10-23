## 2019-10-23

-   关键改动: default slots 不再用 . 做分隔符, 而是用 _ 这是因为翻译组件的问题.
-   删除了默认用 memory 做缓存层, 没有必要
-   对demo做了一些小改动.
-   hearing 增加了 debugMatch 方法
-   修复 组件 依赖 组件 不成功的 bug
-   修复了 storyRegistrar 获取的逻辑, 现在统一用依赖注入最方便