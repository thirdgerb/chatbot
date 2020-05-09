<?php

class IsCallable {
    public function __invoke()
    {
        return 123;
    }
}

// 拥有 __invoke 方的对象仍然不能直接作为 callable 对象使用.
var_dump(is_callable(IsCallable::class));
