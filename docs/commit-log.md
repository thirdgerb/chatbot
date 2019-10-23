## 2019-10-23 (2)

-   关键改动: default slots 不再用 . 做分隔符, 而是用 _ 这是因为翻译组件的问题.
-   删除了默认用 memory 做缓存层, 没有必要
-   对demo做了一些小改动.
-   hearing 增加了 debugMatch 方法
-   修复 组件 依赖 组件 不成功的 bug
-   修复了 storyRegistrar 获取的逻辑, 现在统一用依赖注入最方便


-   修复了 DialogSpeechImpl 一处小改动产生的大bug, question 没有了.
-   基本补充了语料库
-   context name 定下了基本原则: 允许 小写字母,数字,-,. 四类符号
-   小写字母非常必要, 因为大小写导致匹配不上, 根本无法排查.
