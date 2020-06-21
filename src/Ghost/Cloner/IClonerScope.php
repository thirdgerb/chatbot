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
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Framework\Spy\SpyAgency;
use Commune\Support\Arr\ArrayAbleToJson;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IClonerScope implements ClonerScope
{
    use ArrayAbleToJson;

    /**
     * @var string[]
     */
    protected $data;

    protected $time;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->time = time();
        unset($cloner);
        SpyAgency::incr(static::class);
    }

    public function toArray(): array
    {
        $data = $this->data;
        $data['time'] = $this->time;

        return $data;
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
            case ClonerScope::SHELL_ID :
            case ClonerScope::SESSION_ID :
            case ClonerScope::GUEST_ID :
            case ClonerScope::CONVO_ID :
                return $this->data[$name] ?? '';
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

    public function __destruct()
    {
        SpyAgency::decr(static::class);
    }

}