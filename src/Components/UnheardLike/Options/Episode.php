<?php


namespace Commune\Components\UnheardLike\Options;


use Commune\Components\UnheardLike\Libraries\FrameBuilder;
use Commune\Support\Option;
use Commune\Support\Utils\StringUtils;
use Illuminate\Support\Collection;

/**
 * @property-read string $id 脚本的唯一ID, 也是 context 的 name
 * @property-read string $title 脚本的名称.
 *
 * @property-read string[] $introduces 本章开头的对话内容.
 * @property-read string[] $rooms 本章允许的地点名. id => name
 *
 * @property-read Initialize $initialize 游戏开始时的初始设定.
 *
 * @property-read GameQuestion[] $questions 结束时要回答的问题.
 *
 * @property-read Action[] $aside 旁白的内容.
 *
 * @property-read Character[] $characters 出场的角色.
 *
 * @property-read Commands $commands 命令名
 *
 * @property-read string[] $win 获胜后的对白.
 *
 * @property-read string $messagePrefix 所有游戏内容对白的前缀.
 */
class Episode extends Option
{
    const IDENTITY = 'id';

    protected static $associations = [
        'initialize' => Initialize::class,
        'aside[]' => Action::class,
        'characters[]' => Character::class,
        'commands' => Commands::class,
        'questions[]' => GameQuestion::class,
    ];


    /*------ 缓存 ------*/

    /**
     * @var null|Collection
     */
    protected $characterMap;

    /**
     * @var null|Collection
     */
    protected $frames;

    /**
     * @var string[]|null
     */
    protected $names;

    public static function stub(): array
    {
        return [
            'id' => '',
            'title' => '',
            'introduces' => [],
            'messagePrefix' => '',
            'rooms' => [],
            'initialize' => [],
            'aside' => [],
            'characters' => [],
            'commands' => Commands::stub(),
            'questions' => [],
            'win' => [
            ],
        ];
    }

    protected function init(array $data): array
    {
        $data['id'] = StringUtils::normalizeContextName(trim($data['id'] ?? ''));
        return parent::init($data);
    }

    public static function validate(array $data): ? string
    {
        foreach (static::stub() as $key => $value) {
            if (!isset($data[$key])) {
                return "$key is required";
            }

            if (gettype($data[$key]) !== gettype($value)) {
                return "$key value is invalid type";
            }

            if (empty($data[$key])) {
                return "$key value is empty";
            }
        }

        return null;
    }

    /**
     * @return Character[]
     */
    public function getCharacterMap() : Collection
    {
        if (isset($this->charactersMap)) {
            return $this->characterMap;
        }

        $characterMap = [];
        foreach ($this->characters as $character) {
            $characterMap[$character->getId()] = $character;
        }
        return $this->characterMap = new Collection($characterMap);
    }

    public function getFrames() : Collection
    {
        if (isset($this->frames)) {
            return $this->frames;
        }

        return $this->frames = new Collection($this->buildFrames());
    }


    public function getRoleIds() : array
    {
        return $this->getCharacterMap()->keys()->all();
    }

    /**
     * 获取角色和基本描述
     * @return string[]
     */
    public function getRolesDesc() : array
    {
        $result = [];
        foreach ($this->getCharacterMap() as $roleId => $character) {
            $result[$roleId] = $character->desc;
        }

        return $result;
    }

    public function getRolesNames() : array
    {
        if (isset($this->names)) {
            return $this->names;
        }

        $origin = [];
        foreach ($this->characters as $character) {
            $origin[] = $character->name;
        }

        $count = count($origin);

        $names = [];
        for ($i = 0; $i < $count; $i ++ ) {
            $t = array_rand($origin);
            $names[$i] = $origin[$t];
            unset($origin[$t]);
        }

        return $this->names = $names;
    }

    /**
     * @return Frame[]
     */
    protected function buildFrames() : array
    {
        $builder = new FrameBuilder($this);
        return $builder->toFrames();
    }

    public function searchNearFrame(string $time) : string
    {
        $t = intval(str_replace(':', '', $time));

        $frameTime = $this->getInitTime();
        foreach ($this->getFrames() as $frame) {
            /**
             * @var Frame $frame
             */
            $frameTime = $frame->time;
            $t1 = intval(str_replace(':', '', $frameTime));
            if ($t1 >= $t) {
                return $frameTime;
            }
        }

        return $this->getLastTime();
    }

    public function getInitTime() : string
    {
        $frames = $this->getFrames();
        /**
         * @var Frame $frame
         */
        $frame = $frames->first();
        return $frame->time;
    }

    public function getLastTime() : string
    {
        $frames = $this->getFrames();
        /**
         * @var Frame $frame
         */
        $frame = $frames->last();
        return $frame->time;
    }
}