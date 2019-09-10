## 2019-09-09

-   translateTemp 调整策略, 空的reply 等几种情况不翻译.
-   Hearing api 进行一直想做的关键调整. runIntent 与 isIntent 拆分. 默认不run
-   Context 加入了 cast 功能. 用于强制类型转换. 不过有点脏.
-   Intent 默认增加了 isConfirmed 和 confirmedEntities. 参考了 DuerOS的做法
-   问答逻辑增加了和意图相关的提问, 可以通过意图匹配生成答案. 参考了 DuerOS的做法.
-   将 intent 的匹配逻辑在 Session 里进行了封装, 进行了大规模的改动. 需要单元测试.
-   单元测试使用了 mockery
-   修复了迷宫生成地图算法一个罕见情况下的bug
-   简化了 EntityProperty 的实现. 功能越多其实越不好.
-   修复了 Selects 一直存在的问题. 补充了单元测试.
-   backwardInt 修改指令为 backward . 感觉 back 太容易重名了.
-   maze 中大部分回复都改为了用 translate. DemoComponent也增加了相关功能. 为的是测试翻译功能.


-   question 设置了默认的 slots 用于翻译. 主要解决格式化的问题.
-   之前 translator 没有用 ICU Message, 调整了相关功能.
-   添加了问题默认的 ICU 模板
