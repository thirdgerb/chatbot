## 2019-12-17

* 改动很大, 需要进行回归测试
* 重构了异常体系, 一些关键的改动如下
    * 删除了 Exception 的各种 interface, 太复杂没有实际价值
    * Exception 处理逻辑, 完全交给 UserMessengerPipe 处理.
    * ConfigureException 改为 ChatbotLogicException, 会被 Director 拦截, 否则关闭 Server
    * RuntimeException 会在管道中包装成 ConversationalException
    * ConversationalException 会告知用户系统异常, 但不终止对话
    * 新增了 CloseSessionException, 在对话中抛出会重置会话.
    * 重构了 Director 的异常管理逻辑
    * 重构了 UserMessengerPipe 的异常管理逻辑
* 重做了 ExceptionHandler, 现在仅仅是 Reporter, 并且整合到了 Logger 中. 日志记录的是 Exception 则会报告
* 将一部分缓存相关的常量集中到类 CacheKey 中, 方便统一修改
* 删除了系统默认事件, 未来再统一迭代进来. 现阶段反而没必要做这个功能
* ChatApp 的 Available 状态移到了 ChatServer, 目前没有实装. 理论上可以修改所有服务端的运行状态.
* ChatServer 现在在 ChatbotConfig::$server 中注册了.
* ChatApp 允许通过继承, 修改 bootstrap 属性和 chatKernel 属性.

