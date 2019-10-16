## 2019-10-15

-   Host 允许从 request 获取 chatId 和 sessionId, 从而避免数据读写, 方便实现高性能的 api 端.
-   session 增加 isSneaky 方法.
-   session 如果是 sneaky 状态, 不再从数据中读取 snapshot, 这样又少一层IO. 副作用是 command 就无法读到当前 session 的状态了.