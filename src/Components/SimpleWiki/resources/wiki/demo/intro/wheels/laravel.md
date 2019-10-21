description: 使用 laravel 搭建 studio
suggestions:
    - intro
    - ./
examples:
---

chatbot 项目虽然只用于对话, 但用到的很多功能 (数据库, redis管理, 计划任务, 命令行等等) , 和完整的服务端结合效果会更好. 所以我需要一个服务端全栈框架, 作为平台.

目前 commune/chatbot 部分组件用了laravel 的illuminate 组件. 而整体与 laravel 结合得很自然, 效果令人满意.

