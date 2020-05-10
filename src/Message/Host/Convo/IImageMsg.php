<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Host\Convo;

use Commune\Protocals\HostMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Protocals\Host\Convo\Media\ImageMsg;
use Commune\Support\Struct\Struct;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $resource
 */
class IImageMsg extends AbsMessage implements ImageMsg
{

    public function __construct(string $resource)
    {
        parent::__construct(['resource' => $resource]);
    }

    public static function stub(): array
    {
        return [
            'resource' => '',
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public static function create(array $data = []): Struct
    {
        return new static($data['resource'] ?? '');
    }

    public function getNormalizedText(): string
    {
        return '';
    }

    public function isBroadcasting(): bool
    {
        return true;
    }

    public function getResource(): string
    {
        return StringUtils::normalizeString($this->resource);
    }


    public function isEmpty(): bool
    {
        return empty($this->_data['resource']);
    }

    public function getLevel(): string
    {
        return HostMsg::INFO;
    }
}