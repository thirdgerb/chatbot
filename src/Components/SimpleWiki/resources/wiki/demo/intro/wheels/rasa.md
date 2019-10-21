description: 使用 rasa 做自然语言中间件(NLU)
suggestions:
    - intro
    - ./
examples:
---

commune/chatbot 对 NLU 做了一个抽象层, 理论上可以使用任何 NLU api .

由于国内的 NLU api 还不够成熟或不够便捷, 所以我选择了用 rasa 自己搭建一个NLU. 官网在: https://rasa.com

由于语料匮乏, 训练成本昂贵, 所以现阶段的 NLU 精度非常糟糕, 但足以证明功能可用.

