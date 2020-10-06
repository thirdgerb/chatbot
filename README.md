# CommuneChatbot 0.2

0.2 版本正在开发中...应用完成之后才开始撰写文档.

现阶段可能因为改动, 导致 demo 无法启动, 见谅.

Demo 介绍视频已经可以查看: https://www.bilibili.com/video/BV1tK4y1a75B

- 展示网站: https://communechatbot.com/chatlog
- v0.1网站: https://communechatbot.com/
- v0.1分枝: https://github.com/thirdgerb/chatbot/tree/v0.1.x
- v0.1文档: https://communechatbot.com/docs/#/

微信公众号 CommuneChatbot，qq群:907985715 欢迎交流！

核心功能:

1. 基于配置的多轮对话逻辑, 可以无限扩充对话节点.
2. 可以在对话中增加逻辑节点, 用对话给机器人编程, 用对话来生成新的对话
3. 实现了 Ghost in Shells 架构, 机器人可以异构到多个平台, 同时运行

相关项目:

- studio: https://github.com/thirdgerb/studio-hyperf
- 前端项目: https://github.com/thirdgerb/chatlog-web
- nlu单元: https://github.com/thirdgerb/spacy-nlu

对源码感兴趣的话, 推荐查阅 [/src/Blueprint](/src/Blueprint) 文件夹,
以及 [/src/Contracts](/src/Contracts) 文件夹,
其中有项目完整的 interface 设计.

## 本地运行 Demo

项目有一个本地 demo, 配置文件在 [/demo/config/host.php](/demo/config/host.php)

通过以下指令可以进入对话, 输入 ```#help```, ```/help``` 可以查看用户与管理员指令.

### Shell 版 Demo 运行


先确认依赖安装:

- php >= 7.3
- swoole >= 4.4 (可选）
- redis 安装, 默认端口 6379

克隆仓库:

    git clone https://github.com/thirdgerb/chatbot.git

进入目录，使用 composer 安装依赖:

    composer install

运行 console 版 demo

    php demo/client_console_shell.php -d -r

参数 ```-d``` 表示 debug 模式， ``` -r ``` 表示重置对话相关数据

console 版 demo 使用 php 数组做缓存, 本地文件用于配置存储.

### 运行本地多端架构

多端架构 demo 依赖 Swoole >= 4.4, redis 监听 6379 端口.

先运行 ghost:

    php demo/serve_ghost.php -d -r

#### 运行双工 shell 端

    php demo/serve_duplex_shell.php

然后可以通过 ``` php demo/client_to_duplex_shell.php ```  查看效果.

#### 运行同步 shell 端

    php demo/serve_sync_shell.php

然后可以通过 ``` php demo/client_to_sync_shell.php``` 查看效果.


#### 运行异步 shell 端

    php serve_listener_shell.php

然后可以通过 ``` php demo/client_to_listener_shell.php``` 查看效果.


## 运行 studio

项目正式的应用在仓库 https://github.com/thirdgerb/studio-hyperf , 通过 hyperf 提供正规的配置与服务.

回头补充文档.
