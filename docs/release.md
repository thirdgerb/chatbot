## 2019-09-06

-   增加了迷宫小游戏. 打算用到语音音箱里来测试.
-   conversation nlu 稍作优化, 强调了Global Entity, intent Entity, matched entity的区别
-   Redirector Action 增加了go开头的方法方便使用.
-   Context 增加了 fillProperties 方法, 方便批量赋值.
-   Hearing Api 增加了 hasEntityValue 方法.
-   Question 的 parseAnswer 方法现在传入 session 而不是 message, 方便做更多互动.
-   Predefined intent 现在默认加载.
-   redirector 增加 goQuit 功能.