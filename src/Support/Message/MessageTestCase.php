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

use Commune\Support\Babel\Babel;
use Commune\Support\Utils\StringUtils;
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
            $this->ReflectionOfPropertiesTest($name ,$message);
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
                $values = $message->{$relationName};
                $this->assertTrue(is_array($values), $name);

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

    public function ReflectionOfPropertiesTest(string $name, Message $message)
    {
        // 协议校验通过
        $docs = call_user_func([$name, 'getDocComment']);
        $props = StringUtils::fetchPropertyAnnotations($docs);

        // 协议属性正确.
        $reflection = $message->getReflection();
        $errors = [];

        $map = $reflection->getDefinedPropertyMap();

        $errorDoc = '';
        foreach ($map as $propName => $property) {
            $error = $property->validateValue(
                $property->get($message)
            );

            if (isset($error)) {
                $errors[] = $error;
                $errorDoc .= $error;
            }
        }
        $this->assertEmpty($errors, $name . ':' . $errorDoc);

        if (empty($map)) {
            $this->assertEmpty($props, $propName . ' property annotation exits but no reflection property defined');
        }

        $propError = '';
        foreach ($props as list($propName, $desc)) {
            if (!array_key_exists($propName, $map)) {
                $propError .= ", property $propName not found in reflection";
            }
        }

        $this->assertEmpty($propError, $name . $propError);
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