## 2019-10-19

-   基本又修改了一轮 corpus
    -   corpus 还是作为 process service
    -   ComponentOption 会把语料注册到 corpus, 而不是直接保存到 optionRepository
    -   corpus option manager 增加了 register 方法, 不用手动判断目标option是否存在.
