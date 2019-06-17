<?php


namespace Commune\Chatbot\Framework\Conversation;


use Commune\Chatbot\Blueprint\Conversation\Chat;

class ChatImpl implements Chat
{
    /**
     * @var string
     */
    protected $platformId;

    /**
     * @var string
     */
    protected $userId;

    /**
     * @var string
     */
    protected $chatbotUserId;

    /**
     * @var string
     */
    protected $chatId;

    /**
     * ChatImpl constructor.
     * @param string $platformId
     * @param string $userId
     * @param string $chatbotUserId
     */
    public function __construct(string $platformId, string $userId, string $chatbotUserId)
    {
        $this->platformId = $platformId;
        $this->userId = $userId;
        $this->chatbotUserId = $chatbotUserId;
        $this->chatId = md5("p:$platformId:u:$userId:c:$chatbotUserId");
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getChatbotUserId(): string
    {
        return $this->chatbotUserId;
    }

    public function getPlatformId(): string
    {
        return $this->platformId;
    }

    public function getChatId(): string
    {
        return $this->chatId;
    }


}