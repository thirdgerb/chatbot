# 系统预定义的 Context 类型.


- IContext : 标准的 context 容器.
- IContextDef : 标准的 context definition 定义.
- ACodeContext : 用代码的方式编写的 context 容器, 同时反射生成 def.
- AIntentContext : 用 ACodeContext 方式编写 Intent, 作为一种全局事件.
- AMemoryContext : 用 ACodeContext 方式编写 Memory, 作为一种全局记忆.