# 系统架构设计


基本概念:

- Ghost : 纯逻辑的多轮对话管理内核
- Shell : 纯逻辑的平台对接内核.
- Platform : 某一个服务端的逻辑, 可能要启动 Ghost 和 Shell
- Host : 缝合多个组件构成一个完整的多平台异构机器人
    - Ghost : 单一 Ghost
    - Shell : 多 Shell
    - Platform : 多个服务端
    - Messenger : 公共的消息管理
    - Router : 异构时的公共路由.