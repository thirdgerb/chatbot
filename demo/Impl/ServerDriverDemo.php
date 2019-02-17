<?php

/**
 * Class ConsoleServerDriverDemo
 * @package Commune\Chatbot\Demo\Impl
 */

namespace Commune\Chatbot\Demo\Impl;


use Clue\React\Stdio\Stdio;
use Commune\Chatbot\Contracts\ServerDriver;
use Commune\Chatbot\Demo\Character\DemoPlatform;
use Commune\Chatbot\Framework\Character\Platform;
use Commune\Chatbot\Framework\Character\Recipient;
use Commune\Chatbot\Framework\Character\User;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Message\Message;
use Commune\Chatbot\Framework\Message\Text;

class ServerDriverDemo implements ServerDriver
{

    /**
     * @var Stdio
     */
    protected $stdio;

    public function __construct(Stdio $stdio)
    {
        $this->stdio = $stdio;
    }


    public function fetchSender($request): User
    {
        return new User(
            'test',
            'test',
            'test',
            $this->getPlatform(),
            []
        );
    }

    public function fetchRecipient($request): Recipient
    {
        return new Recipient(
            'laravel_console',
            'laravel_console',
            'laravel_console',
            $this->getPlatform(),
            []
        );
    }

    public function fetchMessage($request): Message
    {
        $text = (string) $request;
        return new Text($text);
    }

    public function reply(Conversation $conversation)
    {
        foreach ($conversation->getReplies() as $reply) {
            /**
             * @var Message $reply
             */
            if ($reply instanceof Text) {

                switch($reply->getStyle()) {
                    case Text::WARN:
                        $style = 'warn';
                        break;
                    case Text::ERROR:
                        $style = 'error';
                        break;
                    case Text::INFO:
                        $style = 'info';
                        break;
                    default:
                        $style = '';
                }

                $text = $reply->getText();


                $lines = explode("\n", $text);
                foreach($lines as $line) {
                    if ($style) {
                        $line = "$line";
                    }
                    $this->write($line);
                }
            }
        }
    }

    protected function write(string $text)
    {
        $this->stdio->write($text . PHP_EOL);
    }

    public function error(\Exception $e)
    {
        $this->write($e->getMessage());
        $this->close();
    }


    public function close()
    {
        $this->stdio->end('bye');
    }

    public function getPlatform(): Platform
    {
        return new DemoPlatform();
    }


}