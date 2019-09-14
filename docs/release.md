## 2019-09-14

-   为了把迷宫游戏做得更加规范, 进行了一些修改.
-   拆分mazeInt 和 playMaze, 方便测试depend 等情况.
-   memory 的记忆不再按 chatId 进行隔离, 而是增加了 lock  和 unlock 的功能.
-   修复了 depend property entity with memory 的bug

-   重大改动: history->fallback 如果无路可退了则会 quit 会话. 先看看效果.

-   暴露了一个问题, translator 不能传入空的 pattern, 会导致 intl 错误. 调整了trans的逻辑, 但messages.php 文件需要开发者自己注意不要传空.
