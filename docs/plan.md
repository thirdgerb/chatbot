##  开发计划


### 2019年2月17日

-   周末对引擎进行了大规模重构完善, 重构了近5000行代码. 引擎的功能基本能让自己满意.
-   下一阶段计划
    1.  在laravel 里作为组件实装, 立刻让命令行生效.
    2.  完成 "记日记" 的app
    3.  微信内部实装.
-   下下步计划
    -   实现 "知识库" app, 在微信中可用.
-   时间预期
    -   3月间

### 2019年2月13日

-   有空的话, 继续做 chatbot-laravel 组件.
-   完成组件, 考虑做 wechat 的组件
-   intentRoute 的语法树基于数组实现, 不严谨. 不利于用户理解. 考虑封装成一个面向对象的语法树.
-   异常返回要用code 码, 方便exceptionHandler 层根据 code码来返回异常结果, 而返回码定义在配置里, 可以随时增减.

### 2019年2月12日

-   实现 through 机制. 避免对depend 的单一依赖.
-   实装laravel, 增加数据表等元素. 实装完了就拆包. 越早正式化越好.
-   尽快设计一个应用. 只有有应用场景了, 才有测试和改进的意义. 凭空测试没有太大价值.
-   2月目标能在微信实装.


### 2019年2月

-   完成对director的重构
    -   增加 cancel 和 fail 机制
    -   增加 intended 的 回调机制
    -   guest 不在history中创造新的横向节点, 改为生成纵向节点, 更符合上下文 ?
    -   intentRoute 增加向下管道机制, 即允许 depend方式向上搭建管道, 也允许在routing中向下搭建管道
    -   增加 异常管理.
    -   完成 ask, choose, confirm 三种常用对话
-   建立初步的测试case. 暂时不用单元测试, 先做case 测试
-   完成intent 重构, 传入数据为IntentData, 不再区分类型.
-   实装 intentMatcher 中的 regex 和 command 规则
-   制定 commandPipe, 方便对逻辑进行调试
-   实际开发 laravel 包, 包括migration, config, orm等等. 使项目变为可用.
-   完成 wechat 端的开发. 在微信端部署demo
-   完成开发后按composer库进行一轮拆分.
-   其它
    -   仍然决定不加入复杂的 fulfill, yielding 等机制, 交给用户自己定义, 参考web开发
    -   仍然决定不允许在 intentRoute 的action 中进行重定向.
