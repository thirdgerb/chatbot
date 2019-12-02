## 2019-12-02

- 给 Stage 增加了 onExiting, 现在可以针对单个 Stage 定义退出事件了. 但性能会有损耗.
- 调整了 Hearing::isFulfillIntent 方法, 改名为 Hearing::isPreparedIntent.
- 删除了 Hearing::debug. 考虑未来用别的方式实现.
- Context::__exiting 与 Context::__depend 现在都不再是默认方法了.