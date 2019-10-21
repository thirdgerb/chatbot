description: "特性: 多平台适应"
suggestions:
    - intro
    - ./
examples:
---

对话机器人要多平台适用, 其实比较简单, 只要对每个平台的消息进行统一的抽象就行了.

比如无论是qq, im, 还是语音, 一句话都被抽象为相同的 "textMessage" 对象, 后续的处理就会一致. 不同平台可能还是略有差别, 但核心功能是互通的.

一些对话机器人, 如 hubot 开发了很久, 支持的平台很广泛. commune/chatbot 还是一个开发中项目, 目前只支持 shell, tcp连接 (用telnet可连接), 微信 三种渠道.
