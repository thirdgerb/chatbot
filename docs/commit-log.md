## 2019-10-12 (2)

-   重构 Demo 的 Context, 做一个更通用的对话用例.
    -   删除掉废弃的事件监听.
    -   调整了若干文件路径
    -   创建了许多新的 context
-   default slots 从 host config 迁移到了 chat config
    -   绑定关系也从 host 转移到了 conversation
    -   增加了 default.chatbotName slot
-   其它
    -   情景游戏脚本的 context 添加了 configure tag
    -   新建了 Talker 链式调用 action, 生成纯对话的 callable
    -   大幅度重构了 Menu 组件, 简化了许多功能. 越简单越好.
    -   优化了 QA 的代码, 创建了SessionMock, 进一步完善了单元测试.


