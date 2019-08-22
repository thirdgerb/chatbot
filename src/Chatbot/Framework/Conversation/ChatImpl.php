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
    protected $chatbotUserName;

    /**
     * @var string
     */
    protected $chatId;

    /**
     * ChatImpl constructor.
     * @param string $platformId
     * @param string $userId
     * @param string $chatbotUserName
     * @param string|null $chatId
     */
    public function __construct(
        string $platformId,
        string $userId,
        string $chatbotUserName,
        string $chatId = null
    )
    {
        $this->platformId = $platformId;
        $this->userId = $userId;
        $this->chatbotUserName = $chatbotUserName;
        $this->chatId = $chatId ?? md5("p:$platformId:u:$userId:c:$chatbotUserName");
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getChatbotName(): string
    {
        return $this->chatbotUserName;
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