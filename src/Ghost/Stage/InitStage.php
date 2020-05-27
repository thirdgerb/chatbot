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

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\MindMeta\StageMeta;
use Commune\Ghost\Support\ContextUtils;
use Commune\Support\Option\AbsOption;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $name
 * @property-read string $title
 * @property-read string $desc
 * @property-read string $contextName
 * @property-read string $stageName
 */
class InitStage extends AbsOption implements StageDef
{


    public function __construct(
        string $stageFullName,
        string $contextName,
        string $title,
        string $desc,
        array $config = []
    )
    {
        $config['stageName'] = ContextUtils::parseShortStageName($stageFullName, $contextName);
        $config['contextName'] = $contextName;
        $config['title'] = $title;
        $config['desc'] = $desc;
        parent::__construct($config);
    }

    public static function stub(): array
    {
        return [
            'name' => '',
            'contextName' => '',
            'title' => '',
            'desc' => '',
            'stageName' => '',
        ];
    }

    public static function relations(): array
    {
        return [];
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->desc;
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



    /*------- wrapper -------*/

    public function getMeta(): Meta
    {
        $data = $this->toArray();

        $name = $data['name'] ?? '';
        unset($data['name']);

        $contextName = $data['contextName'] ?? '';
        unset($data['contextName']);

        $title = $data['title'] ?? '';
        unset($data['title']);

        $desc = $data['desc'] ?? '';
        unset($data['desc']);


        return new StageMeta([
            'name' => $name,
            'contextName' => $contextName,
            'title' => $title,
            'desc' => $desc,
            'wrapper' => static::class,
            'config' => [],
        ]);
    }

    public static function wrap(Meta $meta): Wrapper
    {
        if (!$meta instanceof StageMeta) {
            throw new InvalidArgumentException(
                'only accept subclass of ' . StageMeta::class
            );
        }

        return new static(
            $meta->name,
            $meta->contextName,
            $meta->title,
            $meta->desc,
            $meta->config
        );
    }


}