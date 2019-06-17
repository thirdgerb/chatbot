<?php

/**
 * Class MailMessage
 * @package Commune\Chatbot\Blueprint\Conversation
 */

namespace Commune\Chatbot\Blueprint\Conversation;


use Carbon\Carbon;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * Interface ConversationMessage
 * @package Commune\Chatbot\Blueprint\Conversation
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id
 * @property-read string $chatId
 * @property-read string $userId
 * @property-read string $traceId
 * @property-read string $platformId
 * @property-read Carbon $createdAt
 * @property-read Message $message
 * @property-read string|null $replyToId
 * @property-read Message|null $replyTo
 *
 *
 */
interface ConversationMessage extends ArrayAndJsonAble
{
    public function getId() : string;

    /*------- 关键ID -------*/

    public function getTraceId() : string;

    public function getChatId() : string ;

    public function getUserId() : string;

    public function getChatbotUserId() : string ;

    public function getPlatformId() : string;

    /*------- 状态 -------*/

    public function getCreatedAt() : Carbon;


    /*------- 消息 -------*/

    public function getMessage() : Message ;

    public function getReplyToId() : ? string ;

    public function getReplyTo() : ? Message;

}