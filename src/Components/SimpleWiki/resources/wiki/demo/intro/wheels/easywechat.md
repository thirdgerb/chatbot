description: 用 easywechat 实现公众号客户端
suggestions:
    - intro
    - ./
examples:
---

本项目的 demo 选择在微信上发布, 这是一个工业级的使用场景.

为降低开发工作量, 选择了比较主流, 和IoC 结合相对较好的 easywechat, 感谢作者. 官网在: https://www.easywechat.com/

由于 easywechat 的设计更接近单一进程模型, 而本项目使用了 swoole, 所以还是有一些适用上的问题, 需要慢慢优化.
