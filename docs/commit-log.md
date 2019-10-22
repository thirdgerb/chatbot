## 2019-10-22 (2)

-   完成了购买果汁用例的重构, 现在的方法更加简洁直观.
-   ActionIntent 增加了欢迎语方法, 只是为做示范.
-   IntentCorpusOption 增加了 keywords, 暂时还没使用.
-   Hearing
    -   增加了runComponent 方法,  可以提前运行 component
-   commands
    -   where 命令做了一些优化.
-   predefined
    -   添加了 complement, diss, greet, thanks, random 五种默认的意图.
-   callables
    -   添加了 IsNumeric, 做示范.
    -   添加了 ContextSetter