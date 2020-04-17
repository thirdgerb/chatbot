
在 v0.2 版的设计里, 多轮对话的逻辑是可以动态修改的.
这样才能做到在对话中教会机器人新的对话.

而对话逻辑应该怎样存储在多种媒介中, 才能够动态地更新呢?

目前的思路是

- Mindset
    - DefReg
        - MetaRegistry
            - meta1
            - meta2
            - meta3

底层数据存储在 Meta 注册表中. 可以通过 Meta 来操作和读取, 也只能通过 Meta 来操作读取.
相应的 Meta ID 注册到 DefReg 中, DefReg 会根据 Meta 来生成 Def, 并缓存.

需要 reload 时, 本质上就是清空所有的 Def 缓存, 导致重新从 Meta 中读取.
而且这个清空应该是全局的. 理论上每个服务器 worker 进程实例都需要感知到清空命令.
可以设置一个管道专门来做这个事情, 给 Mindset 一个全局持久化的过期时间戳.

然而动态更改逻辑, 有可能造成进行中的对话发生致命错误. 需要仔细思考细节.

* 多轮对话管理

----

暂时把 Process 的子进程等功能都拿掉了. 先把异步做出来再说.
异步还涉及垃圾回收, 是很麻烦的事情.
做 memory 的时候再思考一下吧.

* 多轮对话管理

----

一些关键的概念需要尽早厘清, 并且在实现上要避免歧义.

原来的概念.

- shell : 平台的服务端, 每个端都有自己的通信和形态. 例如 wechat/webSocket 等
- ghost : 机器人的灵魂, 单一的状态管理机
- chat : 对话发生的群体 ID, 可能是 1v1 对话, 也可能是群聊.
- shellSession : shell 的一个 Chat 内一次多轮对话的生命周期.
- ghostSession : 对于 Ghost 而言的一次多轮对话
- user : 发送消息的对象. 与回复消息无关. 回复消息唯一的对象是 Chat
- message : 传输的消息本身的抽象
- shellMessage : 对 shell 内部传输消息的封装.
- ghostMessage : shell 与 ghost 通信时使用的消息.
- context : 当前所处的对话语境, 关系到上下文记忆, 与对话逻辑
- stage : 当前 Context 所处的状态
- dialog : 对话管理工具.

现在考虑用新的概念梳理一遍.


- ghost : 机器人的唯一灵魂.
    - clone : 机器人的一个分身. 与所处环境有关. 所有的思维都用 cloneId 进行区隔.
    - conversation : ghost 内的 session, 可以串联多个 clone.
    - context : 当前所处的语境.
- shell : 与客户端通信的服务端.
    - shellId : 对应通信通道, 应该是全局唯一的.
    - session : shell 内部的 session, shellId 对应唯一的 session
    - sender : 输入消息对象的身份.
    - message : 消息的抽象
- messenger : 系统内部的通信
    - shellMessage : shell 与客户端交换的消息.
    - ghostMessage : shell 与 ghost 交换的信息

* Chatbot架构

----

之前一直有一个思路, Chat 下分若干个 Session 通过 SessionId 来区分.
但实现做成了同一个 Chat 可以同时拥有多个 Session. 这样其实造成了复杂的影响和干扰.

接下来应该把所有的 Chat 做成单一 Session, 根据 SessionId 来判断 Session 的一致性.
不一致时要清空 Session 从头开始.

* Chatbot架构

----

有个技术方案反复变化过很多次, 这次要不再变了.
Shell 自己有 Chat, Ghost 也应该自己有 Chat, 这是作为非单一机器人所必须的设置.

多个 Shell 在 Ghost 内的互通, 需要 Ghost 层来实现. 因为 Ghost 层需要做通讯准备.
不能让 Shell 自己去认 Ghost, 否则通信就不能使用多 Shell 的通道了.

* Chatbot架构

----

考虑 Async 问题如何投递 Context 到其它的 Chat. 目前首要考虑第二种情况, 只投递一个 Context 节点.
这种 Context 应该允许一个 Redirect 是 deliver 方法. 用于投递到别的 Chat.

它将作为一个 InputMsg 进入机器人,


* Async问题

----

Process 的所有 Thread 是否要允许主动标注 title 相关信息?

让用户可以主动操作, 切换, 生成 Operator 算子. 而不是单纯按照既定流程.

* 对话管理

----

目前异步问题最好的解, 就是将它作为 Ghost 联通的一个独立 Shell.
每一个 UserId 对应一种服务. 而 ThreadId 则是独立的 Chat.

可以看出来, 根据 ThreadId 生成的 Chat 并非持续的. 随着任务变动.

任务投递有几种思路.
第一种, 直接把整个 Thread 投递出去.
第二种, 只投递一个独立的 Context, 当这个 Context fulfill 的时候会触发回调.

API 仍然可以使用 ```YieldTo($targetContext, $to = null)``` 类似的方式来定义.
如果 Yield 时要指定当前对话对象, 系统不应该提供直接能力, 但也要能够给出间接能力.

这样的对象看起来是 stageName, contextName, Context, Thread. 过于宽泛了.

有必要的话还需要封装一个 Location/Redirection 之类的抽象.

* Async问题
* 对话管理

----

沿着多个 Chatbot 对接的思路, 再考虑一下异步任务和双 Chat 场景.

只要不是同步服务, 就仍需要考虑持续通信的必要.
对于 Chatbot 而言, 这些接入方是同一个 Chatbot 的其它 shell.
会话的一贯性仍然要维持.

回归到 API 和 Async 的场景, API 是并行的, Async 也是并行的.
这才是问题的关键.

* Chatbot架构

----

双 Chatbot 还有更合理的架构方案.

让两个 Chatbot 对接就是了.
根本不必搞得这么复杂. 凡是能简化的架构都要尽量简化.

* Chatbot架构

---

Shell 的几个抽象都要改. 无论是通讯还是 Request.

Shell 不仅要接受 Response, 还可能要接受 inputMsg.

inputMsg 跨 shell 的传播和渲染机制就需要好好研究了.

InputMsg 和 OutputMsg 这样就需要统一的抽象.

Messenger 也需要改. 需要考虑每个 Shell 定义自己的 Messenger

* Chatbot架构

----

双 Chat 后门.

这种 Chat 的后门可以通过中间件来做.
简单来说, 中间件将所有的 input 和 output 都转投到另一个指定的 Chat.
而该 Chat 也可以直接把消息插入到当前 Chat 的管道里.

A Chat 和 B Chat 凭什么来通信呢?

A Chat 里, ChatId + ShellName 可以生成一个新的 ChatId. 也可以再加上一个 UserId, 如果有必要的话.
这里面的架构都可以研究. 这些消息, 直接投递到 B Chat 的收件箱里. 不经过 B Chat 的多轮对话逻辑.

而 B Chat 可以在自己的多轮对话逻辑里, 向 A Chat 发出同步请求.
这样, B Chat 需要能够感知 A Chat 的 ChatId.
逻辑上是有办法做出来的, 可以通过缓存, 也可以把 ChatId 做成两个部分, 切字符串等等.

* Chatbot架构

----

讨论几种后门的实现方法. 先讨论群聊模型.
群聊模型要求一个改动, 就是不仅 outputMsg 需要广播, inputMsg 也需要广播给所有的 Shell.
哪些 shell 在线是可以指定的.
另外消息需要可以拉取, 要有一个 paginate 的概念. 而且消息的内容是时间排序, 可复现的.
这样需要专门定义一个 Chat 模型.

简单来说, 来自不同 Shell 的 InputMsg 也应该让当前 Shell "听" 到.
至于要不要渲染, 则是另一回事了. 这样 InputMsg 和 OutputMsg 似乎也要有所改动才行.

Request 要选择性地进行渲染.

* Chatbot架构

----

考虑到 Context 是可以跨越 chat 进行投递的, 那么 Context 的模型就得修改了.

这其实非常接近 taskFlow, 一个 Task 可能需要不同的人进行管理. 管理者能随时看到自己的新 task.

Process 可以做得单纯一些, 去掉 Process 的嵌套. 嵌套带来的管理难题太麻烦了.

没有嵌套的情况下, 可以用中间件来实现上级命令. 唉, 最好有更好的办法, 比如用 Sleep 的 Thread 来处理上级命令.

在单一 Process 内部可能可以实现的. 两种做法. 一个是把 Sleep Thread 都具备一个高于 Current 的拦截权限.

另一种思路则是构造三线架构, 一个 sleeping, 一个 blocking, 一个 watching.
watching 中的 Thread, 允许有类似 Intending 这样的操作.

* 对话管理

----

Babel 应该用静态方法来进行对象的序列化与反序列化.
所有的序列化都应该通过 Babel.
序列化策略不仅允许预加载, 也应该允许懒加载. 当然预加载必要性更高.
因为如果直接来一个反序列化, 没有过预加载, Babel 就歇菜了.
Babel 应该是 Chatbot 级要实现的服务.

* 工程方案

----

各种后门汇总:

- 群聊模式: 同步发送消息给同一个 chat 的其它 shell. 大家面对同一个机器人.
- 双 Chat 模式: 用户使用的 Chat 同时会产生一个面向后门的 Chat, 两个 Chat 状态独立, 但可以信息互通.
- API 后门: 相当于独立的对话, 就是用户和机器人之间的. 关联的 shell 也应该有限.
- 异步模式: 关键在于, 一个 Task 是一个独立的 Chat

这么看起来, Shell 应该要有分组的概念. 而且这个分组应该是动态的.
很多分组如果不能实现 Subscribe 的话, 就需要自己主动拉离线消息.

* Chatbot架构
* 后门

----

后门四: 异步任务后门

这种情况下, 机器人 yield 一个 Thread, 等待回调.
该 Thread 会投递给一个指定的 User, 或者指定的 Shell, 让 Shell 负责解决 (最好还是 user)

显然每个 Thread 就是一个独立的 Chat, 而不是一个 Session. 它的生命周期以该 Thread 结束为止.

这样需要设计 Context 跨 Shell/User 的投递, 像传球一样. 是专门针对异步任务来设计的方案.

如果异步任务的解决方是人, 那就是人类来响应异步任务了.

* Chatbot架构

----

群聊模式和非群聊模式.

群聊模式下, 用户和后门使用者面对同一个机器人.

非群聊模式下, 用户和机器人对话是一个 Chat, 而该对话映射到后门 shell 的是另一个 chat.
用户的对话信息投递到新的 Chat, 但两者拥有完全独立的对话状态.

这两种方法的利弊, 回头再研究.

* Chatbot架构

----

后门三: 语境相关命令后门

机器人和用户在对话中, 我们可以看到这个对话, 并可以同步地向机器人下达命令.
下达的命令必须是语境相关的. 机器人也只能做语境相关的响应.

关键问题在于, 是群聊模式还是非群聊模式.

* Chatbot架构

----

后门二: 机器人命令后门

这个命令后门, 可以给机器人下达各种命令. 而不是给用户传递消息.
命令可以在界面上先做好, 未来可以进行迭代.

这些命令是与用户无关的.

我感觉我在为所有的机器人建立一个通用的管理体系.

* Chatbot架构

----

后门一: 对话监听系统.

机器人和用户的所有交互, 都被投递到另一个客户端.

而那个客户端可以独立看到所有的对话内容. 有必要的话, 还可以允许向用户推送.
推送的信息可以有:

- message
- context

如果信息量不是特别大, 一个 websocket 连接的 Web 界面就足以承担了. 甚至不需要管道, 只需要 subscribe.

* Chatbot架构

----

Ghost 现在的思路是面向所有的 "前门", 每一个 Shell 都是同一个机器人.

但现在, Ghost 还可能要开后门. 这并不是群聊模型. Ghost 仍然以机器人的身份和用户进行交互.

这么做的一个好处是, 后门未来甚至可以随时动态地向机器人编程. 每一个已有的对话流程, 都是一个测试用例的样本.

我们讨论一下可以有哪些后门.

* Chatbot架构

----

在 Ghost 上做专门的 async 和 api 端, 看起来还是不够优雅.

API 端可以理解, 因为要去掉多余的中间件. 如果没有 API 端, 那 Ghost 本该具备的 ChatLock 之类的功能,
就必须转移到 Shell 上去. 这样 Ghost 又失去了独立性.

但 Async 看起来就不够优雅. 因为更理想的方式, 是 Async 端直接对接另一个 Shell.
所谓异步, 相当于 QQ 之类的通讯工具上两个聊天似的. 那是标准的异步.

我们姑且按这个思路讨论下去.

* Chatbot架构

----

目前看起来, 微服务架构是最好的做法. Shell 是一个独立的端, 而 Ghost 是一个独立的服务.
最好在技术上也能够把两者合并到一起.
Ghost 本身是单一通道, 则可以在没有 Shell 的情况下, 独立地运行.
当然也可以把两者合并, 做成单一的服务端实例. 这是目前的目标.

* Chatbot架构

----

Shell 和 Client 的通讯有几种情况: 同步 / 双工 / 异步.
具体实现, Shell 不应该管那么死. 而应该抽象为两种:

1. 被动发送消息给用户, 用户有请求时
2. 主动发送消息给用户.

主动发送存在两种情况, 则是双工或离线. 这个应该让 Shell 的 Server 自己去判断.
基于这种思路, 目前的 Shell 又 xx 需要重做.

* Shell通讯.

----

Shell 会从 Ghost 拿到同步结果, 还要能拿到异步结果.
很明显这个异步结果是通过管道来获取的.
至于 Shell 在什么地方去获取, 用在什么地方, 就是另一回事了.

* Ghost通讯

----

Shell 通讯模型应该做规范化.
目前的思路是, Shell 和 Ghost 通讯有同步和异步两种, 同时起作用.
Ghost 和 Shell 不建议做双工通讯.
这样 Shell 还得自己维护粘包等逻辑, 做非常复杂的同步协议, 以实现对客户端的同步响应.
如果同步响应是必须的, Ghost 就不能和 Shell 做双工.

* Ghost通讯

----

Shell 和 Client 之间的通讯有三种形式.
同步通信, 双工通讯的主动推送, 离线推送.
离线推送可以理解为一种主动推送, 但区别在于双工场景下,
Server 实例必须知道自己可以推送哪些内容.

* shell通讯

----

Ghost 和 Shell 究竟怎么维护, 现在成了最麻烦的问题.
第一种思路是微服务, Shell 和 Ghost 之间进行同步 + 异步的通讯.
第二种思路则是分布式, 每一个实例同时是 Shell + Ghost, Ghost 保持某种一致性.

* chatbot架构

----

把开发思路整理成特别有序的文章, 又浪费精力, 又把思路给拆散了. 很麻烦.
现在考虑用类似推特的形式. 一想到什么就写成单条记录.
然后自己打上 hashTag, 方便未来整理.

