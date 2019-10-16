## 2019-10-16

-   OptionRepository 去掉了 调用时传入的 container
    -   改为持有 process container.
    -   OptionRepo 的 storage 不再允许从 conversation 里获取实例.
    -   这样做降低了设计的复杂度, 而且配置理应在启动时就完成加载.
