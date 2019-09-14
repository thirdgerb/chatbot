## 2019-09-14

-   为了把迷宫游戏做得更加规范, 进行了一些修改.
    -   拆分mazeInt 和 playMaze, 方便测试depend 等情况.
-   memory 的记忆不再按 chatId 进行隔离, 而是增加了 lock  和 unlock 的功能.
