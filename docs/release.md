
## 更新记录

## 20190213 v4 更新

-   把获取配置的方式从方法改成了 $app->getConfig(常量名). 这样可扩展.

## 20190213 v3 更新

-   实现了 choice 方法,  运行令人满意
-   增加了lang 常量. 方便未来替换文本.
-   增加了一些辅助类.
-   给 intentRoute 也增加了 ask, confirm, choose 三剑客.

## 20190213 v2 更新

-   把IntentData 更名为Intent
-   增加了 RedirectionBreak, 用异常方式从context 跳转出去
-   测试了回调逻辑, 很好使. 还是需要评估, 跳转允许在action中实现, 还是只能靠route 去定义. 主要是route没有上下文.
-   目前action 如果return location 就跳转, 暂时用这种方法
-   context 增加了format 功能, 方便格式化文本.
-   实现了 confirmation. 下一个要实现的是choice
-   实现了 director 的 then 方法, 按逻辑实现跳转.

## 20190213 v1 更新

1. 优化重做了 context 和 contextData 的事件, 两套事件相互独立
2. director 现在根据contextData 的状态来决定启动事件
3. 优化了IntentRoute 的api, 方便理解
4. 修复了IntentRoute 存在的bug
5. 清理了Root 和 Test 两个context 的debug信息, 方便测试.

### 2019年2月12日

-   今天重构了项目, 重构了目录结构, 做得更像一个package. 为下一步添加 laravel包做准备
-   重做了demo, 用 stdio-react, 取消了demo 对laravel的依赖.
-   修复了一个bug, contextCfg 不能正确读取到scopeTypes
-   增加了phpunit, 方便写单元测试. 不过现在单测的思路仍然是按需写.

### 2019年2月11日 v2

-   intent 重构完成了.
-   context 拆分成 contextCfg 和 contextData, 避免和php类耦合
-   Director 做响应的调整.
-   增加了Analyzer 层, 用来做调试, 未来作为全局命令, 临时制作了command
-   增加了whoami 和 where 两个方法, 关键是未来要增加help
-   基本跑通了流程, 现在 depend, 跳转到提问, 和 redirectIf 都成功了.

下一步:

-   完善demo 做测试
-   或者, 拆分composer包, 使项目结构更规范
-   或者, 直接开始实装 laravel 的包.


### 2019年2月11日 v1

-   决定要做开发计划, 先进行阶段性保存. 争取今天能发一个可运行版本

### 2019年2月4日

-   完成第一个demo版, 顺利跑起来了.