<?php

namespace Commune\Chatbot\App\Messages;

use Commune\Chatbot\App\Messages;
use Commune\Chatbot\Framework\Messages\AbsConvoMsg;
use PHPUnit\Framework\TestCase;

/**
 * 测试 message 是否正确定义的标准单元测试.
 *
 * 测试目标为:
 * 1. mock 的值是它本身. 可以正确执行 mock
 * 2. mock 值可以两次序列化 (序列化值一致)
 * 3. mock 的 toArray 值, 多次序列化后不变.
 *
 */
class ConvoMsgTesting extends TestCase
{
    protected $classes = [
        Unsupported::class,
        Text::class,
        Query::class,
        ArrayMessage::class,

        // system
        Messages\System\MissedReply::class,
        Messages\System\QuitSessionReply::class,

        //ssml
        Messages\SSML\Sub::class,
        Messages\SSML\Speak::class,
        Messages\SSML\Silence::class,
        Messages\SSML\Background::class,
        Messages\SSML\Audio::class,
        Messages\SSML\SayAs\Number::class,
        Messages\SSML\SayAs\Telephone::class,

        //replies
        Messages\Replies\Link::class,
        Messages\Replies\ParagraphText::class,
        Messages\Replies\Reply::class,

        //recognitions
        Messages\Recognitions\VoiceRecognition::class,

        //media
        Messages\Media\Audio::class,
        Messages\Media\Image::class,

        //events
        Messages\Events\ConnectionEvt::class,
        Messages\Events\QuitEvt::class,
        Messages\Events\StartEvt::class,

        //qa
        Messages\QA\Choose::class,
        Messages\QA\Choice::class,
        Messages\QA\Confirm::class,
        Messages\QA\Confirmation::class,
        Messages\QA\Selects::class,
        Messages\QA\Selection::class,
        Messages\QA\VbQuestion::class,
        Messages\QA\VbAnswer::class,

        //contextual qa
        Messages\QA\Contextual\AskEntity::class,
        Messages\QA\Contextual\ChooseEntity::class,
        Messages\QA\Contextual\ChooseIntent::class,
        Messages\QA\Contextual\ConfirmEntity::class,
        Messages\QA\Contextual\ConfirmIntent::class,
        Messages\QA\Contextual\SelectEntity::class,



    ];


    public function testConvoMsg()
    {
        foreach ($this->classes as $messageName) {

            // 类定义没错.
            $this->assertTrue(is_a($messageName, AbsConvoMsg::class, TRUE), $messageName);

            /**
             * @var AbsConvoMsg $mock
             */
            $mock = call_user_func([$messageName, 'mock']);

            // 检查mock定义结果是否正确.
            $this->assertNotNull($mock, $messageName);
            $this->assertTrue(get_class($mock) === trim($messageName, "\\"), $messageName);

            $expectArr = $mock->toArray();

            try {

                // 第一次序列化
                $first = serialize($mock);
            } catch (\Throwable $e) {
                $this->assertTrue(false, $messageName . ':'. $e->getMessage());
            }

            $mock = unserialize($first);
            $this->assertEquals($expectArr, $mock->toArray(), $messageName);

            // 第二次序列化
            $second = serialize($mock);
            $this->assertEquals($first, $second, $messageName);
            $mock = unserialize($first);
            $this->assertEquals($expectArr, $mock->toArray(), $messageName);
        }
    }


}