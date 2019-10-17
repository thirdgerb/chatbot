## 2019-10-17

-   重构了 nlu 语料库管理意图

-   absMessage 的时间进行了修改, 避免再序列化 carbon了.  carbon 序列化太大.
-   backward 当返回节点不存在时, 会执行 rewind 而不是 home

-   实现了极简版的 context gc, 将那种一次性的 context 定期清除掉.
    -   重构了 snapshot 和 breakpoint:
        -   breakpoint 不再独立存储, 而是将backtrace 全部放入 snapshot
        -   snapshot 可以获取所有的 context id, 方便 gc
        -   snapshot 比较大, 序列化时建议用 gzcomporess
        -   重构了 history 的相关逻辑以适应改动.