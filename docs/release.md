## 2019-09-12

-   illuminate 相关package 升级到 ^6.0 (主要为了 lazyCollection)
-   修复了ContextRegistrar 从未在父仓库注册的bug
-   intent 的 isConfirmed 和 confirmedEntities 独立出来, 从而有初始值.
-   完善了 Context 的 casts 功能, 能够强制转换数据类型.
-   问答中增加了一步检查 ordinalInt, 用序数意图来判断选项.
-   session 序列化就抛出异常, 目的是序列化session 的错误立刻暴露.
-   hearing api 增加了 isFulfillIntent, 会将未完成的intent 自动完成.
-   confirm 问题的选项默认值"是"和"否"现在从 defaultMessages配置里获取.
-   context 默认的 onAnnotation 获取entity, 目前只支持 @propery 注解
-   question 现在要指定 __sleep 属性才能保证序列化时存储之. 避免持有复杂对象.
-   MazeTask 现在改为意图, mazeInt
-   调整了 event dispatcher 逻辑, 现在callable 类型的 listener 不能依赖注入, 而用类名的类型可以依赖注入, 必须要在请求生命周期内 fire 事件.