description: simple file chat 配置说明.
suggestions:
    - sfi.test.example
examples:
    - simple file chat 如何做配置?
---

simple file chat 使用md 文件表示一个 intent.

intent 命名规则是 sfi + $group + 文件路径 (将 / 替换为 .)


配置文件分为两部分, 用独立一行的'---'隔开. 上部分是一个yaml配置, 下部分是意图的回复内容.

yaml 配置如下:

- description : intent 的介绍, 必须要配
- suggestions : 是该intent 的猜您想问. 可选.
    + suggestion 有多种配置方法:
        - intent 名称, 或者类名.
        - 省略了 'sfi.$domain.', 同 group 下的intent
        - 同文件夹以下的 intent, 省略了前缀
        - "./", 表示同目录下所有的 intentName.
- examples : 自然语言的样例.
