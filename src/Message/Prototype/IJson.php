<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Prototype;

use Commune\Message\Blueprint\JsonMsg;
use Commune\Message\Prototype\AMessage;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IJson extends AMessage implements JsonMsg
{
    /**
     * @var string
     */
    protected $json;

    /**
     * @var array
     */
    protected $jsonData;

    /**
     * @var bool
     */
    protected $valid;

    /**
     * IJson constructor.
     * @param string $json
     * @param float|null $createdAt
     */
    public function __construct(string $json = '{}', float $createdAt = null)
    {
        $this->json = $json;
        parent::__construct($createdAt);
    }

    public function isEmpty(): bool
    {
        $data = $this->getJsonData();
        return empty($data);
    }


    public function __sleep(): array
    {
        return [
            'json',
            'createdAt',
        ];
    }

    public function getJson(): string
    {
        return $this->json;
    }

    public function isValid(): bool
    {
        if (isset($this->valid)) {
            return $this->valid;
        }
        $this->getJsonData();
        return $this->valid;
    }

    public function getJsonData(): array
    {
        if (isset($this->jsonData)) {
            return $this->jsonData;
        }

        $data = json_decode($this->json, true);
        if (is_array($data)) {
            $this->valid = true;
            return $this->jsonData = $data;
        }
        $this->valid = false;
        return $this->jsonData = [];
    }


}