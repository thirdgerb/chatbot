<?php

use Commune\Blueprint\Ghost\Stage\OnHeed;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Convo ;
use Commune\Blueprint\Ghost\Stage\Matcher;

// 表单型任务的配置样例
return [
    'name' => 'commune.examples.form.action',
    'desc' =>  '表单型任务样例',
    'asIntent' => [
        // 允许用 自然语言识别 命中
        'corpus' => [
            // ... 自然语言的语料
        ],

        // 允许通过合法命令来命中
        'signature' => '/action {field1=?} {field2=?}',
        // 允许在上下文中用文字指令来命中
        'spell' => 'action',

    ],
    // 定义多轮对话的参数
    'entities' => [
        [
            'name' => 'field1',
            'query' => '请输入 field1 ',
            'suggestions' => [],
            // 定义校验器, 校验成功会赋值到 Context
            'validator' => Field1Validator::class,
        ],
        //....
        [
            'name' => 'field2',
            'query' => '请输入 field2 ',
            'suggestions' => [ 'enum1', 'enum2', 'enum3'],
            'validator' => Field1Validator::class,
        ],
    ],
    // 定义最后执行的方法
    'action' => ActionClassName::class,
];

class Field1Validator {

    //__construct 依赖注入
    public function validate(Matcher $matcher) : bool
    {
        if ($matcher->isVerbal()) {
            return true;
        }
        return false;
    }

}

class Field2Validator {

    //__construct 依赖注入
    public function validate(Matcher $matcher) : bool
    {
        // 是否有选项.
        return $matcher->hasChoiceIn([0, 1, 2]);
    }

}

class ActionClassName {

    //__construct 依赖注入

    // 方法可以依赖注入
    public function action(
        OnHeed $stage,
        Convo $convo,
        Context $context,
        Service $service
    ) : Operator
    {
        // 从 Context 中用数组的方式获取 Entity
        $output = $service->invoke($context['field1'], $context['field2']);
        // 将输出发送给用户
        $convo->react()->info($output);
        // 结束当前语境, 回归上一个语境.
        return $stage->fallback()->fulfill();
    }

}



interface Service {

    public function invoke($field1, $field2) : string;
}
