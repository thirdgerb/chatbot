description: "功能: 支持自然语言单元"
suggestions:
    - intro
    - ./
examples:
---

自然语言识别是对话系统必不可少的环节, 有了这个能力, 就可以直接抵达某个意图, 而不用一层层地点击选项 (例如本demo. 不过由于语料有限, 暂未实装).

commune/chatbot 做了 NLU (自然语言单元) 的抽象, 能与各种 NLU api 相结合.

由于国内的AI供应商暂时没有提供足够简洁的api, 所以本 demo 目前用开源项目 rasa 模拟了 nlu, 在精度方面比较欠缺, 但随时可以替换为更成熟的 NLU.

