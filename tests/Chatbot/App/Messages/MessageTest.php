<?php


namespace Commune\Test\Chatbot\App\Messages;


use Carbon\Carbon;
use Commune\Chatbot\App\Messages\Media\Image;
use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Message\ConvoMsg;
use Commune\Chatbot\Blueprint\Message\Media\ImageMsg;
use Commune\Chatbot\Blueprint\Message\MediaMsg;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerbalMsg;
use Commune\Chatbot\Framework\Messages\AbsConvoMsg;
use Commune\Chatbot\Framework\Messages\AbsMedia;
use Commune\Chatbot\Framework\Messages\AbsMessage;
use Commune\Chatbot\Framework\Messages\AbsVerbal;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{

    public function testCmdText()
    {
        $message = new Text($i = '测试小游戏');

        $this->assertEquals($i, $message->getText());
        $this->assertNull($message->getCmdText());
        $this->assertEquals($i, $message->getTrimmedText());

    }

    public function testNameDependencies()
    {
        $message = new Text('');

        $arr = [
            Text::class,
            Message::class,
            AbsMessage::class,
            AbsConvoMsg::class,
            ConvoMsg::class,
            VerbalMsg::class,
            AbsVerbal::class,
        ];
        sort($arr);
        $this->assertEquals($arr, $message->namesAsDependency());

        // image
        $message = new Image('');
        $arr = [
            Image::class,
            AbsMedia::class,
            ImageMsg::class,
            MediaMsg::class,
            AbsConvoMsg::class,
            ConvoMsg::class,
            AbsMessage::class,
            Message::class
        ];
        sort($arr);
        $this->assertEquals($arr, $message->namesAsDependency());
    }

    public function testDeliverAt()
    {
        $text = new Text('test');
        $text->deliverAt(Carbon::createFromTimestamp(1000)->addSeconds(60));

        /**
         * @var Text $text
         */
        $text = unserialize(serialize($text));
        $this->assertEquals(1060, $text->getDeliverAt()->timestamp);
    }
}