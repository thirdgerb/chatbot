## 2019-10-16 (3)

-   OptionRepository 去掉了 调用时传入的 container
    -   改为持有 process container.
    -   OptionRepo 的 storage 不再允许从 conversation 里获取实例.
    -   这样做降低了设计的复杂度, 而且配置理应在启动时就完成加载.
-   开发了 AskContinue  stage 组件. 实现了完整的 循环功能.
-   修复了 stage::hearing 一直存在的 bug
-   hearing 增加了 onHelp 方法, 显式地提供"帮助"逻辑, 作为通用规范之一

-   重构完成了 simple chat component

