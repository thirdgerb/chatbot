
## 更新记录


### 20190313 v1 更新

-   将config从demo中移到专门的配置文件.
-   优化了where命令.
-   允许 command 设置别名.
-   user 类增加interface
-   修复了intentRoute middleware 的bug

### 20190219 v1 更新

-   增加了restart 策略, 更新当前context. 但不会刷新数据. 要刷新数据目前考虑让用户手动.

### 20190218 v1 更新

一些小的改进

-   拆分出了HostDriver模块, 用于多个地方依赖注入, 操作Host 相关的模块.
-   修改了调试用的demo
-   修复了一处bug, Intent 在自动fulfill 过程中 ask 的default 参数没有获取到.
-   commandPipe 里的command 允许使用管道的 $next
-   简化了 IntentRoute 的 match 方式.

### 20190217 v1 更新

大规模更新. 改动非常大.

-   重构了Intent. 现在用命令式的策略重构了Intent, 包括
    -   使用Laravel command 式的 signature 来定义Intent
    -   基于symfony inputDefinition 来定义 Intent 的参数, 同时也定义了name, default, description 等.
    -   梳理了IntentId 的意涵, 进行了规范.
    -   修改了Intent match的整个流程, 对命令式的输入做了特殊对待.
    -   Intent 命中却参数不充分的情况, 现在可以自动提问, 开始fulfill 参数的过程. 这个很有用.
    -   允许一个route action 判断intent的参数是否符合标准, 不符合可以按照自己的定义要求用户重新输入. 表单式的功能还需要进一步完善.
    -   Context 在回调时自动转为 Intent
    -   完善了 IntentFactory 的各种matcher, 需要在实践中进一步完善. regex 目前还没做.
-   重构了IntentRoute 的action, redirect 等处的api, 使之更严谨, IDE识别更好. 不用数组, 而用树来实现.
-   优化了之前Guest 和Intended 存在的混乱逻辑. 现在回调方法由guest 的那个路由来确定, 这样不会影响Intended 的本意.
-   拆分了userCommandPipe 和 analyzerPipe, 为管理者和用户提供两套命令.
-   按需重构了Director, 增加了部分Runner, 方便 action 中间制定跳转路径. 不推荐的做法.
-   新增了多条命令, 方便调试.
-   按需补充了个别单测.
-   加了一个时间检查单元, 一个对话纯逻辑只需要 几百 us 就可以完成.
-   优化了多个对象字符串输出方式, 默认支持 toJson
-   优化了部分异常处理.
-   增加了默认的 bootstrapper, preload 各种config.

### 20190213 v4 更新

-   把获取配置的方式从方法改成了 $app->getConfig(常量名). 这样可扩展.

### 20190213 v3 更新

-   实现了 choice 方法,  运行令人满意
-   增加了lang 常量. 方便未来替换文本.
-   增加了一些辅助类.
-   给 intentRoute 也增加了 ask, confirm, choose 三剑客.

### 20190213 v2 更新

-   把IntentData 更名为Intent
-   增加了 RedirectionBreak, 用异常方式从context 跳转出去
-   测试了回调逻辑, 很好使. 还是需要评估, 跳转允许在action中实现, 还是只能靠route 去定义. 主要是route没有上下文.
-   目前action 如果return location 就跳转, 暂时用这种方法
-   context 增加了format 功能, 方便格式化文本.
-   实现了 confirmation. 下一个要实现的是choice
-   实现了 director 的 then 方法, 按逻辑实现跳转.

### 20190213 v1 更新

1. 优化重做了 context 和 contextData 的事件, 两套事件相互独立
2. director 现在根据contextData 的状态来决定启动事件
3. 优化了IntentRoute 的api, 方便理解
4. 修复了IntentRoute 存在的bug
5. 清理了Root 和 Test 两个context 的debug信息, 方便测试.

#### 2019年2月12日

-   今天重构了项目, 重构了目录结构, 做得更像一个package. 为下一步添加 laravel包做准备
-   重做了demo, 用 stdio-react, 取消了demo 对laravel的依赖.
-   修复了一个bug, contextCfg 不能正确读取到scopeTypes
-   增加了phpunit, 方便写单元测试. 不过现在单测的思路仍然是按需写.

#### 2019年2月11日 v2

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


#### 2019年2月11日 v1

-   决定要做开发计划, 先进行阶段性保存. 争取今天能发一个可运行版本

#### 2019年2月4日

-   完成第一个demo版, 顺利跑起来了.