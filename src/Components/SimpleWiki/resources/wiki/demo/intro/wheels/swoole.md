description: 使用 swoole 启动服务
suggestions:
    - intro
    - ./
examples:
---

由于 commune/chatbot 要预加载各种意图(intent), 语境(context) 的数据, 启动时间在 几十毫秒以上, 而且会随着项目扩大线性地增加.

所以本项目一定不能用一个请求一个进程的多进程模型. 在调研了 reactphp, roadrunner 等组件后, 毫无疑问 swoole 是最好的选择.

本项目的 tcp demo, 和微信 server, 都基于 swoole 启动. 项目大量使用了依赖注入, 在不带 数据库IO 时裸跑开销在 2ms ~ 4ms , 已经特别令人满意了.

感谢swoole. 官网在: https://www.swoole.com/

