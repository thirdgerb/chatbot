<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Stage;

use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Blueprint\Ghost\MindMeta\StageMeta;
use Commune\Ghost\IMindDef\IIntentDef;
use Commune\Support\Option\AbsOption;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $name
 * @property-read string $title
 * @property-read string $desc
 * @property-read string $contextName
 * @property-read string $stageName
 * @property-read IntentMeta|null $asIntent
 */
abstract class AbsStageDef extends AbsOption implements StageDef
{
    const IDENTITY = 'name';

    public static function stub(): array
    {
        return [
            'name' => '',
            'title' => '',
            'desc' => '',
            'contextName' => '',
            'stageName' => '',
            'asIntent' => null,
        ];
    }


    public static function relations(): array
    {
        return [
            'asIntent' => IntentMeta::class,
        ];
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        $title = $this->title;
        return empty($title) ? $this->getName() : $title;
    }

    public function getDescription(): string
    {
        $desc = $this->desc;
        return empty($desc) ? $this->getName() : $desc;
    }


    public function getStageShortName(): string
    {
        return $this->stageName;
    }

    public function getContextName(): string
    {
        return $this->contextName;
    }

    public function isContextRoot(): bool
    {
        return $this->name === $this->contextName;
    }

    public function asIntentDef(): IntentDef
    {
        $asIntent = $this->asIntent;
        if (!isset($asIntent)) {
            return new IIntentDef([
                'name' => $this->name,
                'title' => $this->title,
                'desc' => $this->desc,
                'examples' => [],
            ]);
        } else {
            $asIntent->name = $this->name;
            $asIntent->title = $this->title;
            $asIntent->desc = $this->desc;
        }

        return $asIntent->toWrapper();
    }

    /*------- wrapper -------*/

    /**
     * @return StageMeta
     */
    public function toMeta(): Meta
    {
        $data = $this->toArray();

        $name = $data['name'] ?? '';
        unset($data['name']);
        unset($data['contextName']);
        unset($data['title']);
        unset($data['desc']);
        unset($data['stageName']);

        return new StageMeta([
            'name' => $name,
            'stageName' => $this->stageName,
            'contextName' => $this->contextName,
            'title' => $this->title,
            'desc' => $this->desc,
            'wrapper' => static::class,
            'config' => $data,
        ]);
    }

    public static function wrapMeta(Meta $meta): Wrapper
    {
        if (!$meta instanceof StageMeta) {
            throw new InvalidArgumentException(
                'only accept subclass of ' . StageMeta::class
            );
        }

        $config = $meta->config;
        $config['name'] = $meta->name;
        $config['contextName'] = $meta->contextName;
        $config['title'] = $meta->title;
        $config['desc'] = $meta->desc;
        $config['stageName'] = $meta->stageName;

        return new static($config);
    }


}