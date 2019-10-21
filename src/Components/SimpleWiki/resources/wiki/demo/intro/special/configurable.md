description: "特性: 可配置化"
suggestions:
    - examples.config
    - ./
examples:
---

许多机器人都可以用简单的json文件, 甚至图形编辑器, 配置出对话.

commune/chatbot 项目的实现思路是先工程, 再配置.

在保证各个模块可编程的前提下, 再开发配置化策略. 本项目的配置化层次有:

- 系统可配置
- 组件可配置
- 对话可配置

您现在看到的, 就是一个配置出来的对话组件. 源码在:

https://github.com/thirdgerb/chatbot-studio/tree/master/commune/data/sfi/demo
