<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost;

use Commune\Blueprint\Ghost\Cloner\ClonerScope;
use Commune\Protocals\Intercom\GhostInput;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IClonerScope implements ClonerScope
{

    /**
     * @var string
     */
    protected $clonerId;

    /**
     * @var string
     */
    protected $senderId;

    /**
     * @var string
     */
    protected $guestId;

    /**
     * @var string
     */
    protected $shellName;

    /**
     * @var string
     */
    protected $shellId;

    /**
     * @var int
     */
    protected $time;

    /**
     * @var string
     */
    protected $sceneId;

    public function __construct(GhostInput $input)
    {
        $this->clonerId = $input->getCloneId();
        $this->senderId = $input->senderId;
        $this->guestId = $input->getGuestId();
        $this->shellName = $input->shellName;
        $this->shellId = $input->shellId;
        $this->sceneId = $input->sceneId;
        $this->time = time();
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
                throw new InvalidArgumentException(
                    __METHOD__,
                    'longTermDimensions',
                    'dimension must be string'
                );
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
            case ClonerScope::SENDER_ID :
            case ClonerScope::GUEST_ID :
            case ClonerScope::SHELL_NAME :
            case ClonerScope::SHELL_ID :
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