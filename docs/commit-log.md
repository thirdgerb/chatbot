## 2019-10-21 (2)

-   ContextRegistrar 体系进行了关键重构
    -   将 parent registrar 独立成了容器
    -   将 sub registrar 简单化, 不再承担容器的功能
    -   这样所有的逻辑都变得简单明了, 但改动非常大.
    -   还好之前的工程做得好, 居然能改.

-   SimpleWikiComponent 改动
    -   解决了 wikiOption intentName 大小写问题
    -   拆分了路径算法, 修复了bug, 补全单测

-   修复了意图语料库不能同步所有意图数据的问题.
