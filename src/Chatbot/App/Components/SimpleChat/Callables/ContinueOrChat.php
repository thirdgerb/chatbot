<?php


namespace Commune\Chatbot\App\Components\SimpleChat\Callables;


use Commune\Chatbot\App\Callables\Actions\ToNext;
use Commune\Chatbot\OOHost\Context\Callables\HearingComponent;
use Commune\Chatbot\OOHost\Dialogue\Hearing;

/**
 * 用于 hearing->component 的组件.
 * 要么闲聊, 要么运行 next . 算是示范吧.
 */
class ContinueOrChat implements HearingComponent
{
    /**
     * @var string|null
     */
    protected $resourceIndex;

    /**
     * @var string
     */
    protected $next;

    /**
     * ContinueOrChat constructor.
     * @param string $next
     * @param string $resourceIndex
     */
    public function __construct(string $next = null, string $resourceIndex = null )
    {
        $this->resourceIndex = $resourceIndex;
        $this->next = $next;
    }


    public function __invoke(Hearing $hearing): void
    {
        $hearing
            ->isEmpty(new ToNext($this->next))
            ->fallback(new SimpleChatAction($this->resourceIndex));
    }


}