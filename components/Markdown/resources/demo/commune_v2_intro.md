# CommuneChatbot 项目介绍

[//]: # (@stageName intro)

很高兴为您介绍 CommuneChatbot 项目! 这是一个开源的对话交互系统开发框架。

[//]: # (@info)

您现在所看到的多轮对话是使用 markdown 文档自动生成，源码在 [github 仓库](https://github.com/thirdgerb/chatbot/tree/master/components/Markdown/resources/demo)

[//]: # (@break)

您可以：

- 按对话提示的顺序来了解这个项目
- 说 ```退出``` 以退出当前对话
- 输入 ```#help``` 查看可以使用的指令
- 也可以随时向我提问

[//]: # (@info)

机器人无法回答的问题，管理员会通过对话教学功能教会机器人回答。感谢帮助！

[//]: # (@askNext)
[//]: # (@routeToStage more_topics)

## 什么是 CommuneChatbot 项目

[//]: # (@stageName what_is_commune_chatbot)

[//]: # (@intentExample 这是一个什么项目)
[//]: # (@intentExample 这是啥项目啊)

[//]: # (@bili <iframe src="//player.bilibili.com/player.html?aid=712044178&bvid=BV1oD4y1d7eX&cid=232996782&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe>)

"Commune" 是 "亲密交谈" 的意思，CommuneChatbot 是一个对话机器人的开源项目。

[//]: # (@info)

它是作者 [烈风](thirdgerb@github.com) 为了验证自己对话交互系统的产品思路、技术设想而创立的。
项目聚焦于对话系统的工程解决方案，将自然语言理解作为辅助工具。


[//]: # (@askNext)
[//]: # (@routeToStage more_topics)


## 和其它对话机器人系统的区别

[//]: # (@stageName difference_from_other_chatbots)

[//]: # (@intentExample 这个项目有什么特点)
[//]: # (@intentExample 与其它对话机器人有什么区别)
[//]: # (@intentExample 项目的特色是什么)

[//]: # (@bili <iframe src="//player.bilibili.com/player.html?aid=669512318&bvid=BV1xa4y1E7hR&cid=232997599&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe>)

为了说明本项目和常见的在线客服、智能问答、闲聊机器人的不同之处，我临时提出了 "半开放域对话式交互系统" 的命题。

[//]: # (@info)

这个命题可以分成两部分理解：

[//]: # (@info)

半开放域问题:
- 核心功能、核心对话的主题由服务方主导
- 要求严谨的对话流程
- 低资源（语料资源）启动
- 围绕主题，支持与用户进行长尾的开放式对话，不断积累语料与对话能力

[//]: # (@break)

[//]: # (@bili <iframe src="//player.bilibili.com/player.html?aid=202068665&bvid=BV1Bh411R7fb&cid=232997899&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe>)

对话式交互系统:

- 核心在于交互而非对话
- 目的是用对话形式控制各种设备，系统，执行复杂任务
- 交互能力对标其它交互系统，包括异步、多任务调度、协同任务管理等
- 需要严谨的复杂多轮对话管理
- 支持接入各种对话平台、各种设备、各种消息类型
- 支持对话形式的内容创作
- 作为一种交互界面，同样支持自解释、自我管理，可通过对话学会新的能力

[//]: # (@askNext)
[//]: # (@routeToStage more_topics)


## 主要的产品思路和技术功能

[//]: # (@stageName major_production_prototypes)

[//]: # (@bili <iframe src="//player.bilibili.com/player.html?aid=499526166&bvid=BV14K411K7ZB&cid=232998171&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe>)

在 "半开放域对话式交互系统" 的命题下，本项目初步探索了几种产品思路：

- 对话式视频应用
- 聊客 （chatlog）
- 对话式编程
- 对话式 wiki

[//]: # (@info)

为实现这些技术思路与产品思路，项目重点验证了：

[//]: # (@info)

- 用微信语音操作网页版对话机器人
- 完全可编程与完全可配置的多轮对话内核
- 四种非阻塞对话能力
- 可自我描述、自解释、自我管理的元对话
- 可用于对话式学习的元对话
- 基于 markdown 编辑的多轮对话
- 可动态学习的自然语言理解中间件

[//]: # (@askNext)

## 更多话题探讨

[//]: # (@stageName more_topics)

接下来您可以选择自己感兴趣的话题，让我们深入探讨。

[//]: # (@askChoose)
[//]: # (@routeUcl md.demo.complete_demo_video)
[//]: # (@routeUcl md.demo.half_open_conversational_system)
[//]: # (@routeUcl md.demo.conversational_video_app)
[//]: # (@routeUcl md.demo.chatlog_intro)
[//]: # (@routeUcl md.demo.ghost_in_shells)
[//]: # (@routeUcl md.demo.codable_configable_core)
[//]: # (@routeUcl md.demo.conversational_wiki)
