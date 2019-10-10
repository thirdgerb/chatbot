<?php


namespace Commune\Components\Story\Basic;


use Commune\Chatbot\App\Memories\MemoryDef;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\Memory\AbsMemory;
use Commune\Chatbot\OOHost\Session\Scope;
use Commune\Chatbot\OOHost\Session\SessionInstance;

/**
 * @property array $items
 * @property string $playingEpisode 正在玩的章节.
 * @property string|null $playingStage 正在玩的stage
 * @property string[] $unlockEpisodes 解锁的章节
 * @property string $unlockingEpisode 新解锁的章节.
 * @property string[] $unlockEndings 解锁的结局.
 */
class ScriptMem extends MemoryDef
{
    const DESCRIPTION = '游戏剧本的记忆';
    const SCOPE_TYPES = [Scope::USER_ID];

    /**
     * @var string
     */
    protected $scriptName;

    private function __construct(string $scriptName)
    {
        $this->scriptName = $scriptName;
        parent::__construct();
    }

    public function getId(): string
    {
        $id = parent::getId();
        return md5("$id:episode:{$this->scriptName}");
    }

    protected function init(): array
    {
        return [
            'items' => [],
            'playingEpisode' => '',
            'unlockingEpisode' => '',
            'unlockEndings' => [],
            'unlockEpisodes' => [],
        ];
    }

    /**
     * @param SessionInstance $instance
     * @return static
     */
    public static function from(SessionInstance $instance): AbsMemory
    {
        if ($instance instanceof AbsScriptTask) {
            $option = $instance->getScriptOption();
            $scriptName = $option->title . ':' . $option->version;

        } else {
            throw new ConfigureException(
                static::class
                . ' only serve story context'
            );
        }

        $memory = new static($scriptName);
        if ($instance->isInstanced()) {
            return $memory->toInstance($instance->getSession());
        }
        return $memory;
    }


    public function __sleep(): array
    {
        $names = parent::__sleep();
        $names[] = 'scriptName';
        return $names;
    }
}