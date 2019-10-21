## 2019-10-21


-   Stage 机制重大改动
    -   拆分了 start, intend, callback, fallback 四种 stage. 一开始就应该做成状态机.
    -   拆分之后逻辑变得清晰多了, 各处做了相应修改.
    -   四种 stage 各种情形下的跳转逻辑都做了设计, 真的很难想清楚.
    -   Navigator 的相关方法都进行调整 (还好以前的工程做得漂亮)
    -   ContextDefinition 增加了两种响应的 stage caller 方法
    -   发现并修复了若干 bug
-   重构了 Simple Wiki 组件 (原 simple file chat ).
    -   正式使用 OptionRepository + ContextRegistrar 做动态的意图管理.
    -   配置文件完全使用 yml
    -   目录规则进行了一致化 (统一用 ... 表示 n 级的上级目录)
-   gc 机制改动
    -   memory context 不做 gc. 即便不是被 history 持有, 也应该保存.
    -   考虑了所有 snapshots, 解决了嵌套会话里可能出现的问题.
    -   RepositoryImpl 里 save 的部分进行了方法分拆, 让思路更明确.
-   Corpus 优化
    -   增加了 IntentDefHasCorpus 的 interface, 方便corpus 自动从 intentDef 里加载默认配置, 又不耦合 intent 自身的逻辑.
-   小改动
    -   yamlStorage 现在默认用 '.yml' 做后缀.
    -   RootFileStorage 允许 meta 决定 option 生成逻辑. 不过只能根据路径做调整.
    -   Menu 组件去掉了 onFallback 方法, 明明可以在外部定义.
    -   ContextRepositoryImpl 由于是递归式地调用, 把所有自身调用的逻辑独立出了方法. 理想的设计思路, 是区分仓库层和本体层. 这样代码会简洁很多. 回头考虑.
