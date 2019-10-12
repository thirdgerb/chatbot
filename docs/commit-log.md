## 2019-10-12

-   迁移部分文件, 准备下一步功能整合.
-   补充了一些注释
-   bug fix
    -   nlu::toArray() 不再输出静态变量.
    -   AbsMessage::getTrimmedText() 取消了normalize, 统一小写反而复杂了.
    -   修复 ContextRegistrar 用 tag 获取 def 时的bug
    -   修复了 DialogSpeech::trans() 的bug, 自调用死循环.

