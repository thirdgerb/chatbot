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
use Commune\Support\Protocal\Protocal;
use Commune\Support\Struct\AbsStruct;
use Commune\Support\Utils\StringUtils;
use PHPUnit\Framework\TestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class MessageTestCase extends TestCase
{
    protected $messages = [
    ];


    public function testRelations()
    {
         foreach ($this->messages as $name) {

             // 必须是 Message 类
            $this->assertTrue(is_a($name, Message::class, TRUE));

             /**
              * @var Message $message
              */
            $message = call_user_func([$name, 'create']);

            // 类型
            $relationNames = $message->getRelationNames();

            foreach ($relationNames as $relationName) {

                // 如果是列表关联关系
                $isList = call_user_func([$name, 'isListRelation'], $relationName);

                if ($isList) {
                    // 拿到的数据也是数组
                    $this->assertIsArray($values = $message->{$relationName});

                    $relationClass = call_user_func([$name, 'getRelationClass'], $relationName);

                    // 类型一致
                    foreach ($values as $value) {
                        $this->assertTrue(is_a($value, $relationClass, TRUE));
                    }

                    continue;
                }

                $value = $message->{$relationName};
                $relationClass = call_user_func([$name, 'getRelationClass'], $relationName);
                $this->assertTrue(is_a($value, $relationClass, TRUE));
            }

        }
    }

    public function testStubAndCreate()
    {
        foreach ($this->messages as $name) {
            $this->assertTrue(is_a($name, Message::class, TRUE));
            $stub = call_user_func([$name, 'stub']);
            $message = call_user_func([$name, 'create'], $stub);
            $this->assertEquals($stub, $message->toArray());
        }
    }

    public function testProtocals()
    {
        foreach ($this->messages as $name) {
            $stub = call_user_func([$name, 'stub']);
            $message = call_user_func([$name, 'create'], $stub);

            foreach ($message->getProtocals() as $protocal) {
                $this->assertTrue(is_a($message, $protocal, TRUE));
                $this->assertTrue(is_a($message, Protocal::class, TRUE));
            }

            $this->protocalPropertyTest($message);
        }
    }

    protected function protocalPropertyTest(Message $message)
    {
        foreach ($message->getProtocals() as $protocal) {
            $r = new \ReflectionClass($protocal);
            $properties = StringUtils::fetchPropertyAnnotations($r->getDocComment());

            $stub = $message->stub();
            foreach ($properties as list($name, $desc)) {

                $this->assertTrue(
                    array_key_exists($name, $stub)
                    || method_exists($message, AbsStruct::GETTER_PREFIX. $name)
                );
            }
        }

    }


    public function testBabel()
    {
        foreach ($this->messages as $name) {
            /**
             * @var Message $message
             */
            $message = call_user_func([$name, 'create']);
            $str = Babel::serialize($message);
            $un = Babel::unserialize($str);

            $this->assertEquals($message->toArray(), $un->toArray());
        }
    }
}