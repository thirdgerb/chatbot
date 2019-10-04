## 2019-10-04

-   增加了默认的 unsupported message, 作为平台消息不支持的默认解决方案.
-   重做了 media 类型的信息. 在缺乏实践的情况下, 还是不够满意. 目前认为 media source 都是url
-   CacheAdapter 增加了对 psr-16 cache 的支持, 从而方便兼容各种组件的需要而不要做两套.
-   对 framework 的 exception 做了
-   Message Request 改动
    -   Message Request 当赋予 Conversation 时会调用 onBindConversation 方法. 可以做一些初始化.
    -   增加了 Validate 方法和逻辑. 如果 Message Request 是 invalid, 则在 MessengerPipe 会直接返回. 这样方便把一些端上的校验逻辑转移到 Message Request 内部.
    -   增加了 RequestExceptionInterface . 会记录异常, 造成对用户不响应.
-   统一了 finish 逻辑. 当一个请求级对象调用 finish 方法时, 是在为 destruct 做准备.
-   conversational service provider 现在增加了 bound 检验, 方便覆盖系统默认的服务注册


