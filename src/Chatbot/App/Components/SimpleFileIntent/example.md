description: 用简单的文件配置意图的示范.
question: 示范猜你想问(需要手动配):
suggestions:
    - sfi.example
examples:
    - simple file intent 是什么意思?

---

SimpleFileIntent 是一种可以用简单的markdown文件配置的intent.

intent 的 name 根据文件夹路径来生成. 例如 'path/a/b/c.md', 对应的name是'sfi.a.b.c'

配置文件分为两部分, 用独立一行的'---'隔开.
第二部分是意图的默认回复. 命中意图后, 会用 info 来输出这部分文本.

第一部分是一个 yaml 配置, 只定义了四个参数:
- description: 是意图的介绍. 通常必须配.
- suggestions: 是猜你想问的配置, 一个数组. 每个选项都应该是一个intent 的 name. 如果没有配置的话, 意图命中后会直接返回.
- question: 提示用户猜你想问时的问题.
- examples: 用于nature language unit 的例句.
