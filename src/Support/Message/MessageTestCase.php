<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Message;

use Commune\Message\Intercom\IShellInput;
use Commune\Support\Babel\Babel;
use Commune\Support\Protocal\Protocal;
use Commune\Support\Struct\StructReflections;
use PHPUnit\Framework\TestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class MessageTestCase extends TestCase
{
    protected $messages = [
    ];


    public function testMessages()
    {
        foreach ($this->messages as $name) {
                     // 必须是 Message 类
            $this->assertTrue(is_a($name, Message::class, TRUE));

            // 构建数据.
            $data = call_user_func([$name, 'stub']);
            $message = call_user_func([$name, 'create'], $data);
            $this->assertTrue($message instanceof Message);
            $this->assertTrue(is_a($message, $name, TRUE));

            // 分别测试.
            $this->protocalsTest($name ,$message);
            $this->babelTest($name, $message);
            $this->relationsTest($name, $message);
        }


    }

    public function relationsTest(string $name, Message $message)
    {
        // 类型
        $relationNames = $message->getRelationNames();

        foreach ($relationNames as $relationName) {

            // 如果是列表关联关系
            $isList = call_user_func([$name, 'isListRelation'], $relationName);

            if ($isList) {
                // 拿到的数据也是数组
                $this->assertIsArray($values = $message->{$relationName}, $name);

                $relationClass = call_user_func([$name, 'getRelationClass'], $relationName);

                // 类型一致
                foreach ($values as $value) {
                    $this->assertTrue(is_a($value, $relationClass, TRUE), $name);
                }

                continue;
            }

            $value = $message->{$relationName};
            $relationClass = call_user_func([$name, 'getRelationClass'], $relationName);
            $this->assertTrue(is_a($value, $relationClass, TRUE), $name);
        }
    }

    public function protocalsTest(string $name, Message $message)
    {
        // 检查协议存在
        foreach ($message->getProtocals() as $protocal) {
            $this->assertTrue(is_a($message, $protocal, TRUE), $name);
            $this->assertTrue(is_a($message, Protocal::class, TRUE), $name);
        }

        // 协议校验通过
        $e = null;
        try {
            $docs = call_user_func([$name, 'getDocComment']);
        } catch (\Exception $e) {
        }
        $this->assertTrue(is_null($e), $name);

        // 协议属性正确.
        $validators = StructReflections::getAllFieldReflectors($name);

        foreach ($validators as $name => $validator) {
            $error = $validator->validateValue($message->{$name});
            $this->assertNull($error, $name);
        }
    }

    public function babelTest(string $name, Message $message)
    {
        $stub = $message->stub();
        $message = call_user_func([$name, 'create'], $stub);
        $str = Babel::serialize($message);
        $un = Babel::unserialize($str);
        $this->assertEquals($message->toArray(), $un->toArray(), $name);
    }
}