
## Kernel

Kernel 在这里指的是机器人逻辑上的内核 (并非物理上).

异构的对话机器人, 其逻辑上的 Kernel 会分散在不同节点上, 包括不同的 shell 和 ghost.
而控制流程仍然要有序地将它们连接起来.

允许各个流程跳跃到后面, 或者从中间开始.


基本的逻辑流程是:


- ShellServer
    1. ShellInputRequest : Shell 输入请求
    1. ShellInputResponse : Shell 输入处理
    1. GhostRequest : Shell 对 Ghost 的请求
        - GhostServer
            1. GhostRequest : Ghost 接受到请求
            1. CloneRequest : Ghost 请求转化为 Clone Request
            1. CloneResponse : 多轮对话逻辑结果
            1. GhostResponse : Ghost 的 Response
        - GhostServer
    1. GhostResponse : Ghost 发送给 Shell 的响应.
    1. ShellOutputRequest : Shell 渲染输出消息
    1. ShellOutputResponse : Shell 输出响应
- ShellServer

每个环节的 Handler 是:

- ShellInputParser
- Shell2GhostMessenger
- GhostInputReceiver
- CloneDialogManager
- Ghost2ShellMessenger
- ShellOutputReceiver
- ShellOutputRender
