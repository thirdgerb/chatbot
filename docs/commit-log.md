## 2019-10-04 (2)

-   功能改动
    -   MessageRequest 增加了 Scene, 允许根据 scene 的不同, 开启不同 root context 的 session
    -   调整 message request 方法名, 增加了 sendFailureResponse 和 sendRejectResponse
    -   debug 状态下默认 error_reporting 为 E_ALL
-   重构了异常体系
    -   调整了 MessengerPipe 异常处理机制. 区分了两种异常: 可发消息的, 无法响应的.
    -   无法响应的异常又分为 三种
        -   不做特殊处理的 : request exception, 请求和渲染环节的异常.
        -   关闭客户端的 : runtime exception
        -   关闭 worker 的 : stop service exception
    -   分离了 reject 和 failure 两种无法返回消息的异常响应.
    -   exception handler 给出了默认的实现.
-   api 调整
    -   增加了 ClientFactory 用于生成 guzzle 的 client. 提前对 swoole + 协程的情况做兼容.
    -   Chat 增加了 lock 和 unlock 方法, 相应地修改了 chattingPipe, 并增加了锁过期的配置
    -   logger 现在默认用 chatbotName 作为 log name.
    -   conversation logger 现在从 message request 里获取默认的日志参数.
-   bug fix
    -   修复了 ChatApp registerProcessService 类型约束的问题.
    -   Messenger 接受到 RequestException, 仍会尝试响应. 因为已经有 validate 机制了.

