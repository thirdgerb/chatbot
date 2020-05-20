<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Cloner;

use Commune\Blueprint\Ghost\Cloner\ClonerScope;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Support\Arr\ArrayAbleToJson;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IClonerScope implements ClonerScope
{
    use ArrayAbleToJson;

    /**
     * @var string
     */
    protected $clonerId;

    /**
     * @var string
     */
    protected $guestId;

    /**
     * @var string
     */
    protected $convoId;

    /**
     * @var int
     */
    protected $time;

    /**
     * @var string
     */
    protected $sceneId;

    public function __construct(InputMsg $input)
    {
        $this->clonerId = $input->getSessionId();
        $this->guestId = $input->getGuestId();
        $this->convoId = $input->getConversationId();
        $this->sceneId = $input->getSceneId();
        $this->time = time();
    }

    public function toArray(): array
    {
        return [
            'clone' => $this->clonerId,
            'guest' => $this->guestId,
            'convo' => $this->convoId,
            'scene' => $this->sceneId,
            'time' => $this->time,
        ];
    }


    public function makeScopeId(string $name, array $longTermDimensions): string
    {
        $map = $this->getLongTermDimensionsDict($longTermDimensions);
        return $this->makeId($name, $map);
    }

    public function getLongTermDimensionsDict(array $longTermDimensions): array
    {
        $map = array_map(function($dimension){
            if (!is_string($dimension)) {
                throw new InvalidArgumentException('long term dimension must be string');
            }

            return $this->{$dimension};
        }, $longTermDimensions);

        return $map;
    }

    public function makeId(string $name, array $dimensionsDict): string
    {
        ksort($dimensionsDict);

        $str = "uniqueName:$name";
        foreach ($dimensionsDict as $key => $value) {
            $str .= ":key:$key:value:$value";
        }
        return sha1($str);
    }

    public function __get($name)
    {
        switch($name) {
            case ClonerScope::CLONE_ID :
            case ClonerScope::GUEST_ID :
            case ClonerScope::CONVO_ID :
            case ClonerScope::SCENE_ID :
                return $this->{$name};
            case ClonerScope::YEAR :
                return date('Y', $this->time);
            case ClonerScope::MONTH :
                return date('m', $this->time);
            case ClonerScope::MONTH_DAY :
                return date('d', $this->time);
            case ClonerScope::WEEKDAY :
                return date('D', $this->time);
            case ClonerScope::WEEK :
                return date('W', $this->time);
            case ClonerScope::HOUR :
                return date('h', $this->time);
            case ClonerScope::MINUTE :
                return date('i', $this->time);
            default :
                return '';
        }
    }


}