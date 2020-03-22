# Shell


shell 是对话机器人的某一种感官.

- 它可能是双工的, 比如对话端
- 可能是纯输出的, 例如屏幕
- 可能是纯输入的, 例如按键
- 可能是纯控制的, 例如 API
- 可能是异步响应的接口

简单来说, 每个 Chatbot 只有一个 Ghost, 但可以有很多个 Shell.

多个 Shell 是否互通, 决定性的 Scope 是  ChatId. 拥有相同 ChatId 的 Shell 理论上应该是状态同步的.