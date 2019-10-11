## 2019-10-11

-   基本完成了 NLU 和语料库单元的重构.
    -   NLUComponent 是预加载组件.
    -   NLUServicePipe 是默认的 nlu 管道.
    -   Story 组件加入了默认的语料
    -   Predefined 组件加入了默认的语料

-   ChatApp 改动
    -   增加了 registerConfigService 方法, 最高优先级 boot. 加载option meta 给OptionRepository
-   OptionRepository 改动
    -   storage 增加了 flush 方法, 可以清空整个storage 的一种缓存.
    -   save 方法可以存储多个 option, 避免批量存储多次IO
    -   允许 Repository 同步整个 category
    -   允许 Repository 批量删除
-   ComponentOption 改动
    -   修改了加载 option meta 的方法, 保证优先级最高.
    -   默认增加了从 yaml 中读取意图语料库的办法
    -   默认增加了从 yaml 中读取实体词典的办法
-   其它
    -   StringUtils 增加了一个简单的文字转数字方法. 试用在 ordinalIntent
    -   修复了 IntentMatcher 正则存在的问题. 允许一个 entity 匹配多个值.
    -   option 增加了 getIdentityName 方法, 用于判断 option 是唯一还是多种.
    -   context 的 casts 通过方法来读取, 使之可以重写.
    -   message::getTrimmedText() 现在返回 normalize 的字符串.
    -   conversation/nlu 的 done 方法记录完成匹配的 nlu
    -   conversation/nlu 可以 toArray , 方便记录日志.
    -   GuzzleClientFactory 加入默认的 timeout 避免阻塞到死.
    -   简单调整了 testCase
