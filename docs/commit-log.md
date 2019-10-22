## 2019-10-22

-   删除了 simple wiki 下不用的文件.
-   增加了 Entity Extractor 模块, 允许基于配置自己去匹配 entity.
    -   给出了默认的 php 实现, 类似敏感词匹配算法.
    -   Hearing 增加了 matchEntity 的实现, 如果nlu没有就自己上
-   完善了查询天气的意图实现, 现在允许基于 php 自己的实体识别算法走完流程, 性能还不错.
-   NLU 改动
    -   增加了 mergeEntities 方法. 否则增加部分 entity 非常复杂.
    -   NLU 现在也可以用依赖注入了.
-   Hearing 改动
    -   增加了 reHear 方法, 允许在匹配并执行逻辑, 但没有重定向的时候, 继续匹配.
    -   修复了 isIntent 相当于 runIntent 的bug
    -   runIntent 相关方法, 现在若没有重定向, 则会自动 reHear
    -   __hearing 方法现在创建 Hearing 时直接执行.

