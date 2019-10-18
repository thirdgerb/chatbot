## 2019-10-18

-   小改动
    -   nlu 现在不强制提供中间件, 可以注册多个服务, 方便进行管理.
    -   nlu 语料库现在不是存储在单个文件中, 而是存在多个文件中. 方便大量数据.
    -   修复了若干bug

-   corpus 改动
    -   将 synonym 从 entityDict 里拆分出来了. 作为独立的同义词词典
    -   将 corpus 拆分成多个manager, 享有相同的 api, 减少重复代码.
    -   将 ComponentOption 里注册 nlu 资源的方法合并成一个通用的.
    -   intentCorpusOption 的 intentName 改为 name
    -   同步所有改动到其它文件.
