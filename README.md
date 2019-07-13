## developing

开发中...


## 简介

commune/chatbot 项目, 多轮对话交互引擎. 还在开发中, 所以简单说下怎么测试:


### 确定依赖

本项目依赖:

-   php7.2
-   swoole : 可选, swoole 用例依赖.


安装:


    # 安装项目
    git clone https://github.com/thirdgerb/chatbot.git
    cd chatbot/

    # composer 安装依赖
    composer install

运行demo :

    # 命令行demo, 基于 clue/stdio-react 项目
    php demo/console.php

    # tcp demo, 基于 swoole. 可以用 telnet 127.0.0.1 9501 连接.
    php demo/swoole.php




