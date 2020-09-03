
# 半开放域对话式交互系统

[//]: # (@stageName start)

[//]: # (@bili <iframe src="//player.bilibili.com/player.html?aid=329382588&bvid=BV1nA411n7EX&cid=231545385&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe>)

作者从 2015年起，因为对一些产品形态的设想，开始设计对话式交互系统。思路一直都是以工程方案（或曰规则化的对话管理）为主。

[//]: # (@info)

其间以机器学习驱动的自然语言技术也在飞快发展，早已成为各种主流对话系统的标准解决方案。进一步到 "多轮对话管理" 的领域，也出现了类似 LSTM 的技术来驱动自然语言对话。许多人认为对话系统里 "工程" 的做法将被彻底淘汰了。


[//]: # (@info)

然而个人对对话系统的探索越多，反而越觉得 "机器学习并非银弹"，因为作者最初设计的产品形态，用现今机器学习的技术是无法解决的。

[//]: # (@info)

我把其中的差异做了些归纳，总结出 "半开放域对话式交互系统" 的命题，作为一家之言，供技术大佬们批判。

[//]: # (@askChoose 半开放域交互系统相关问题：)
[//]: # (@routeToRelation children)


## 半开放域问题

[//]: # (@stageName half_open_scope)

[//]: # (@bili <iframe src="//player.bilibili.com/player.html?aid=286879513&bvid=BV1qf4y1Q7VF&cid=231563713&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe>)

人类的自然语言对话有无数种可能性，对话的内容究竟是完全开放的，还是限定的，就构成了 "域" 的问题。


[//]: # (@info)

"封闭域"（不开放域）问题：核心特点是服务方主导。
例如带有 "对话" 特征的文字游戏、视觉小说、电话客服系统等，服务方只提供有限的、预先设计好的能力。用户的行为必须严格遵守流程。

[//]: # (@info)

完全的 "开放域" 问题：核心特点是用户主导。
例如各种闲聊机器人，最典型的恐怕是大力出奇迹的 "GPT-3"，用户可以随意与之进行对话，尽管机器人对话内容并非总符合现实或逻辑，但仍能让人对机器和真人的界限感到混淆。

[//]: # (@info)

所谓的 "半开放域" 问题，通常是限定对话内容、围绕某个能产生商业价值的主题。它最核心的功能是由服务端主导的，但又并非完全封闭，允许一些开放式的对话场景，例如问答、咨询甚至部分闲聊等。事实上，绝大多数的商用对话项目都是 "半开放域" 的。

[//]: # (@info)

"半开放域" 对话系统要成立，最核心的一个假设是基于二八法则的。系统最核心的功能为服务方主导，在冷启动时已经具备。而剩下的开放域对话，可能百分之八十的需求集中在百分之二十的问题上，可以在日后长期的维护中不断充实。

[//]: # (@askNext)
[//]: # (@routeToRelation parent |返回)

## 为何机器学习并非银弹

[//]: # (@stageName not_silver_bullet)

我认为 "半开放域" 问题下 "机器学习并非银弹"，工程手段也属必要，有以下几个关键的原因：

[//]: # (@askChoose 机器学习并非银弹的理由：)
[//]: # (@routeToRelation children)
[//]: # (@routeToRelation parent |返回上一层)


### 低资源启动

[//]: # (@stageName low_resource_start)

[//]: # (@bili <iframe src="//player.bilibili.com/player.html?aid=371982708&bvid=BV1XZ4y1P7iH&cid=231558844&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe>)

大家都了解，人工智能的时代 "能源" 不再是化石矿物，而是 "大数据"。
对话系统尤其仰赖 "语料" 的质量。类似京东2000万语料制造对话客服机器人的任务，不用机器学习的技术也难以想象。

[//]: # (@info)

但对于各种 "新生" 的对话式任务，例如智能音箱里的 "技能"、智能家居的 "对话交互" 而言，项目启动初期语料明显是匮乏的。

[//]: # (@info)

对于这种 "低资源" 的场景，尽管机器学习是非常激发想象力的方案，但用 "工程" 或曰 "规则" 的方式快速启动，是普遍认为更加 "现实" 的做法。


[//]: # (@askChoose)
[//]: # (@routeToRelation brothers)
[//]: # (@routeToRelation parent |返回)

### 严谨多轮对话流程

[//]: # (@stageName strict_multi_turn_conversation)

[//]: # (@bili <iframe src="//player.bilibili.com/player.html?aid=626958818&bvid=BV1qt4y1S7Bo&cid=231564327&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe>)

闲聊的对话场景对流程要求不高，但商业的生产领域中，对流程要求会极为严谨。
例如填写资料、支付、问卷调查等等。越是 "封闭" 的场景，对流程的严谨要求就越高。


[//]: # (@info)

闲聊中可以接受的理解偏差，在用户提供格式化信息的场景中就无法接受了。现在基于机器学习的多轮对话也难以克服准确率问题

[//]: # (@info)

所以在线客服之类的项目，即便用机器学习多轮对话技术驱动，也往往通过 "卡片" 等形式，用视觉、触觉的操作手段来约束复杂流程。至于 "京医通" 那样问卷调查式对话，很明显完全通过规则来编写。

[//]: # (@info)

在自然语言理解技术发展到高级智能以前，"工程" 手段在规范性上的优势难以被机器学习的多轮对话引擎所取代。

[//]: # (@askChoose)
[//]: # (@routeToRelation brothers)
[//]: # (@routeToRelation parent |返回)


### 复杂上下文切换

[//]: # (@stageName context_switch)

[//]: # (@bili <iframe src="//player.bilibili.com/player.html?aid=244388138&bvid=BV1Nv41117qH&cid=231560622&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe>)

人类自然语言有一个特点，尽管表达每一个意图的方式相对松散，但上下文的转换就严谨得多。
尤其是买卖东西、咨询问题这类有目的性的场景，对话完一个主题，下一个主题是什么，往往非常规范。专业的对话服务会把上下文的调度策略当作 "话术" 的一部分。


[//]: # (@info)

导致上下文切换的复杂原因也很复杂：

- 例如用户说错了一个信息，要求 "返回" 上一步；
- 例如临时来一个重要信息，打断了当前对话，而结束之后要 "回归" 到被打断的对话；
- 例如当前任务 "依赖" 另一个任务，用户中断了依赖任务的对话，当前任务也需要面临 "中断"；
- 例如用户同时在聊多个话题，想主动 "切换" 到某一个感兴趣的话题……

[//]: # (@info)

机器学习 "多轮对话管理" 技术面临的问题是，如果不对上下文切换进行严谨的建模，面对的只能是 __线性__ 的语料，所有 "非线性" 的对话策略选择，在 "语料" 中都难以体现。

[//]: # (@info)

如同编程语言的运行都会有严谨的调用栈去驱动上下文。我个人认为， "机器学习多轮对话管理" 技术也需要建立在一个符合 "自然对话" 的预期，严谨的上下文调度模型基础上。

[//]: # (@askChoose)
[//]: # (@routeToRelation brothers)
[//]: # (@routeToRelation parent |返回)

### 复杂工程任务

[//]: # (@stageName complex_task)

[//]: # (@bili <iframe src="//player.bilibili.com/player.html?aid=839421615&bvid=BV1t54y127dq&cid=231562169&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe>)

除了闲聊之外，人机对话也有带明确目的性的多轮对话，也被称之为 "任务型" 对话。

[//]: # (@info)

任务型对话往往是 "工程化" 的，需要：

- 参数校验
- 权限验证
- 第三方服务调用
- 异常与错误反馈
- 异步获得结果

[//]: # (@info)

这类场景中，即便 "多轮对话" 可以通过机器学习去驱动，背后的功能仍然是工程化的。
需要大量的增删改差，每个 resource 的逻辑都高度一致，但产生的对话语料却会截然不同。

[//]: # (@info)

纯粹用机器学习多轮对话驱动不见得能降低工程的复杂度，反而可能要求工程应用增加对 "自然语言对话" 的容错。

[//]: # (@info)

在对话的每一步都进行工程化的约束，也许是更低成本的解决方案。

[//]: # (@askChoose)
[//]: # (@routeToRelation brothers)
[//]: # (@routeToRelation parent |返回)


### 人类学习的主观能动性

[//]: # (@stageName human_conscious_activity)

[//]: # (@bili <iframe src="//player.bilibili.com/player.html?aid=541917444&bvid=BV1Bi4y1M7y8&cid=231564972&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe>)

机器学习作为 "人工智能" 科学的一种技术方案，追求的是无所不包的理解能力。
站在科学研究的立场往往必须把用户想象得很笨，要有充分的容错能力。

[//]: # (@info)

但在现实中的生产环境场景中，当用户 "目的性" 非常明确，只把 "对话" 当成一种交互形式时——用户追求的往往是 "最高效率的交互"。

[//]: # (@info)

这时，聪明的人类会主动学习最有效的表达方式以操作系统，而未见得对对话理解能力求全责备。所以设计一个效率最高、错误概率最少的交互模型，可能比自然语言理解更为重要。

[//]: # (@info)

可以预期，未来的对话系统就像曾经的触屏系统一样，优秀的产品经理也是不可或缺的，对话流程上的改进可能比自然语言理解的容错更为高效率。

[//]: # (@askNext)
[//]: # (@routeToRelation parent |返回)


## 对话式交互系统

[//]: # (@stageName conversational_interface)

[//]: # (@bili <iframe src="//player.bilibili.com/player.html?aid=969473144&bvid=BV1Xp4y1Y7U7&cid=231559975&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe>)

"对话机器人" 这个命题，看起来的核心是 "对话"。而 "对话式交互系统" 中，最为核心的词是 "交互"。当立场不同时，技术指标的优先级就产生了方向性的区别。

[//]: # (@info)

举一个决定性的例子，对于以 "交互" 为目标的 "对话式交互系统"，"自然语言理解" 技术不是必须的。基于单词的命令式操作 （例如电影《铁甲钢拳》的语音操作），基于结构规范的 "命令行语言" 操作，
基于序号的 "选项式操作" 都在特定场景下比 "自然语言" 更为高效率。

[//]: # (@info)

"人工智能对话" 作为一个大众熟悉的概念，它的特性很好理解。如果把立足点放在 "对话式交互系统"，就需要重新厘清 "对话式" 的优势所在，以及 "交互" 必要的功能点了。


[//]: # (@askChoose 关于对话式交互系统：)
[//]: # (@routeToRelation children)
[//]: # (@routeToRelation parent |返回上一层)

### 对话交互的优势

[//]: # (@stageName conversational_interface_advantidge)

现在以 "对话" 为核心的对话系统，之所以得到了商业的青睐，因为它能一定程度上取代人工，做到原本只有人类能做到的事情。于是产生出巨大的利益。

[//]: # (@info)

如果以 "交互" 为核心，那么对话系统就不是用来取代人的，而是用于提高人生产力的。因此重点在于它为什么能提高生产力。

[//]: # (@askChoose)
[//]: # (@routeToRelation children)
[//]: # (@routeToRelation parent |返回)

#### 语音交互解放生产力

[//]: # (@stageName voice_interface)

[//]: # (@bili <iframe src="//player.bilibili.com/player.html?aid=329493743&bvid=BV1PA411H7t5&cid=231565541&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe>)

触屏技术发明之后，开创了移动互联网时代。台式机时代要求人类给 "智能设备" 提供 "空间"，包括桌椅、布线，进一步要求独立的房间，于是更进一步要求独立的时间。我们只有在完全属于自己的时间，来到一个属于自己的空间（包括租用的网吧），才能使用智能设备。

[//]: # (@info)

而触屏技术使手机交互变得廉价，从此人们可以在公交车、地铁、马路牙子甚至马桶上使用智能设备。这是 "从空间上解放了人"，释放了巨大的生产力，是触屏技术最本质的竞争力所在。

[//]: # (@info)

"语音交互" 技术一旦成熟，意味着我们不一定要使用眼睛和双手才能使用智能应用。
这在 "姿势" 上进一步解放了人。

[//]: # (@info)

我们可以像科幻片里一样，只需要一个无线耳机，就可以在行走时、运动时、公开宣讲时甚至战斗时（参见《西部世界2》）使用各种智能应用。这种生产力的全面提升，是作者认为对话应用竞争力的本质。

[//]: # (@askChoose)
[//]: # (@routeToRelation brothers)
[//]: # (@routeToRelation parent |返回)


#### 意图直达

[//]: # (@stageName intent_redirect)

[//]: # (@bili <iframe src="//player.bilibili.com/player.html?aid=969475843&bvid=BV1Np4y1e7qn&cid=231564730&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe>)

作者理解，与视觉、触觉等交互相比，通过语言文字获取信息的效率相对低、差了几个数量级；但语言文字却是对人类而言，创造和输出信息最高效的方式。

[//]: # (@info)

我们把一个用文字描述的信息（例如小说），转换成用动态视觉、声音、触觉来感受的信息（比如插画、漫画、电影），成本高达数十倍、百倍、干倍、万倍。

[//]: # (@info)

当一个系统对外暴露的能力只有几种、十几种时，一个图形界面是最高效的交互手段。但当系统的能力有几千、几万种时，唯有基于文本框的搜索功能能直达目的，通过树状结构层层点击寻找是不可想象的。

[//]: # (@info)

所以未来所有的复杂系统都会有基于文字、语言的交互手段，这是系统复杂度、交互效率所决定的必然趋势。

[//]: # (@askChoose)
[//]: # (@routeToRelation brothers)
[//]: # (@routeToRelation parent |返回)


#### 更好的平台适配性

[//]: # (@stageName platform_adapter)

[//]: # (@bili <iframe src="//player.bilibili.com/player.html?aid=754448544&bvid=BV1kk4y127xH&cid=231563389&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe>)

光从交互的角度来看，语言文字交互系统很可能成为最廉价的交互系统。这本质于语言对于人类的普适，以及系统无关的特性。

[//]: # (@info)

现在绝大部分的视觉交互应用都受制于系统平台，往往一个应用需要有桌面版、移动版、网页版；桌面版有 win、mac、linux 等不同操作系统，移动版有 iOS、Android 等系统，网页版也需要考虑不同浏览器的适配。需要很多套代码。

[//]: # (@info)

技术上的趋势是把代码层面统一化，但仍然解决不了每个系统自带的 api 截然不同的问题。

[//]: # (@info)

而对话式系统与之截然不同，无论任何一个系统，使用的文字都是天然一样的。而对话交互逻辑可以放在云端。于是同一个对话机器人，只要做了不同平台的适配，可以出现在任何一个对话系统，无论是微信、qq、钉钉、网页版、智能音箱等等……

[//]: # (@info)

在一些用对话可以实现的应用，比如问卷调查、简单的增删改查、命令式的操作等等……通过一个微信聊天就可以实现了，很可能在这个领域取代绝大多数视觉交互应用。

[//]: # (@askNext)
[//]: # (@routeToRelation parent |返回)

### 交互系统的必要功能

[//]: # (@stageName interface_features)

将 "对话" 视作 "人工智能" 的一部分，对标的应该是 "图灵测试"。而将 "对话" 视作交互系统的一种形式，它对标的应该是其它所有的交互系统。包括机械的、视窗的、触屏的、体感的、甚至神经信号的……

[//]: # (@info)

这时就应该用交互系统的通用标准来要求对话系统。我认为这几种能力是必要的：


[//]: # (@askChoose 交互系统的必要功能：)
[//]: # (@routeToRelation children)
[//]: # (@routeToRelation parent |返回上一层)

#### 非阻塞

[//]: # (@stageName none_blocking)

[//]: # (@bili <iframe src="//player.bilibili.com/player.html?aid=286915512&bvid=BV1Uf4y1Q74i&cid=231560195&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe>)

"非阻塞" 是交互系统最基本的功能，决定它的现实是许多任务都无法立刻完成。要等待一段时间之后才能获得反馈。

[//]: # (@info)

现阶段许多对话系统，例如微信公众号和智能音箱，其实是同步模型。系统必须在零点几秒的时间区间内产生回复，否则就会被客户端视作超时。因此它们很难执行高耗时的任务反馈。

[//]: # (@info)

具体的 "非阻塞"，还会有 "主动推送"、"异步非阻塞"、"同步非阻塞" 等各种细分的现象。
其中对于对话系统最难的应该是同步非阻塞。

[//]: # (@info)

它意味着一个任务执行到一半中断（yield）后，对话系统还可以继续进行别的对话；而当任务得到结果（retain）后，它需要打断当前的对话（block），要求用户执行之前的流程；
而当这个任务结束时，它又能回归（resume）到被它打断的任务流程中。

[//]: # (@info)

这种非线性的对话，生产出来的语料上下文理论上有无数种可能性，这样产生出来的线性语料很难体现出异步任务的存在。

[//]: # (@askChoose)
[//]: # (@routeToRelation brothers)
[//]: # (@routeToRelation parent |返回)

#### 多任务调度

[//]: # (@stageName multi_tasks_manage)

由于长耗时任务客观存在，在实现非阻塞的前提下，与一个机器人同时交互多个平行任务就自然而然了。

[//]: # (@info)

这本质上和视窗操作系统一样，是多个运行任务对交互通道（视频、音频、键盘）的独占，这种独占可以在用户命令下进行切换。因此多个平行任务之上必须时刻运行一个最高级的调度任务，任务之间对通信的占有是管道式、分层级的。

[//]: # (@info)

这也是工程手段对于对话系统必要性的体现。

[//]: # (@askChoose)
[//]: # (@routeToRelation brothers)
[//]: # (@routeToRelation parent |返回)


#### 规则校验

[//]: # (@stageName rules_verify)

所有的交互系统，都面临至少两方面规则的校验，其一是参数校验、其二是权限校验。
对话交互系统概莫能外。

[//]: # (@info)

开发经验让我们知道，权限校验常常非常细致而复杂，也是测试最花精力的环节。机器学习或许无法直接解决校验的问题，意味着仍需要一个控制单元去解决复杂规则。工程上的复杂度并不会因为算法而消失。

[//]: # (@askChoose)
[//]: # (@routeToRelation brothers)
[//]: # (@routeToRelation parent |返回)


#### 严谨交互流程

[//]: # (@stageName strict_routes)

这一点在之前的内容中已经提过，这里简单归纳一句。作为交互系统，其上下文的跳转、切换、回归必须是严谨的，因为被对话驱动的机器本身是一个严谨的状态机。

[//]: # (@askChoose)
[//]: # (@routeToRelation brothers)
[//]: # (@routeToRelation parent |返回)

#### 上下文容错

[//]: # (@stageName recorrecting)

所谓上下文容错，指用户给出一个导致上下文状态变化的指令之后，发现有错误，想要修正之前的错误操作。我个人认为这是最困难的一个特性。

[//]: # (@info)

容错至少有三种情况，无副作用回退，轻副作用回退和严重副作用中断。
- 无副作用回退，只要求保留上下文位置；
- 轻副作用回退，要求在上下文位置之外，还保留与逻辑相关的各种状态敏感数据；
- 而严重副作用的指令是无法回退的，必须要允许用户及时打断它，然后全面清空副作用

[//]: # (@info)

这就要求多轮对话模型要能保存上下文历史，并且要能尽可能减少回退的副作用。极端情况下要考虑保留若干帧的完整状态。各种退出机制也应该是层层递归的，允许被关联任务拦截，或处理自己的终止任务逻辑。

[//]: # (@askChoose)
[//]: # (@routeToRelation brothers)
[//]: # (@routeToRelation parent |返回)


#### 自我解释与管理

[//]: # (@bili <iframe src="//player.bilibili.com/player.html?aid=329473537&bvid=BV1mA411n7SQ&cid=231566509&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe>)

[//]: # (@stageName self_managable)

由于人机交互本质上是操作状态机，提供机器有关的状态信息就非常重要。
所有成熟的交互系统都有基本的自解释功能，用户可以验证状态机的参数、所处位置、拥有的能力。

[//]: # (@info)

这就要求多轮对话系统不能只是一个算法的黑箱，作为交互系统，它需要有：

- 决策选项、影响决策状态参数，都要求首先对它自己是可知的；
- 对用户是可以暴露和展示的；
- 拥有对用户解释自身的表达能力；
- 对状态的查询过程不影响上下文

[//]: # (@info)

类似《西部世界》中管理员对 host 说 "停止运行机能"、"开始调试模式"、"bring yourself online" 等等。

[//]: # (@askNext)
[//]: # (@routeToRelation parent |返回)

## 工程方案与自然语言技术的结合

[//]: # (@stageName nlp_engineer_reconcile)

[//]: # (@bili <iframe src="//player.bilibili.com/player.html?aid=371885980&bvid=BV1PZ4y1T7A7&cid=231562857&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe>)

对话形式作为一种新兴的交互系统，它还在漫长演进历史的早期。很多关键的技术特性，还没有被引入现有系统的架构中，但未来是必备的。

[//]: # (@info)

现阶段常常看到重视算法，轻视工程的现象；又或者把两种技术手段对立起来。这应该都是一时的现象。

[//]: # (@info)

对话交互系统在功能上势必要对标各种现有的交互系统，在自然语言技术还远未成熟的现阶段，工程手段是一种比较现实、甚至相对高效的解决方案。

[//]: # (@info)

以工程为手段冷启动的项目，将积累最初的语料，为未来机器学习自然语言技术提供燃料。这个道理，就像环保组织也主张的核养绿一样。

[//]: # (@info)

进一步的，工程方案对人类的对话本身进行高度抽象，设计出来的严谨模型，如果符合人类对话的自然规律，它本身就应该是更高级人工智能技术的前提。

[//]: # (@info)

就像围棋的人工智能并不是从识别一横一竖开始，星际争霸的人工智能不是从学习操作鼠标开始
——对自然现象的工程化建模，应该成为机器学习的一个更高台阶。两者的关系并不是互斥的，也没有技术追求上的好坏，一切取决于生产环境中的效率。


[//]: # (@routeToStage ending)

# 结束

[//]: # (@stageName ending)

以上就是本人关于 "半开放域对话式交互系统" 的相关思考，感谢您的阅读，期待给予我指教！谢谢！

[//]: # (@goFulfill)

