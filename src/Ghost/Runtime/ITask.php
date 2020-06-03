<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Runtime;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Runtime\Task;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Support\Arr\ArrayAbleToJson;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
class ITask implements Task
{
    use ArrayAbleToJson;

    /**
     * @var Ucl
     */
    protected $ucl;

    /**
     * @var string[]
     */
    protected $paths;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var string|null
     */
    protected $cancel;

    /**
     * @var string|null
     */
    protected $quit;

    /**
     * ITask constructor.
     * @param Ucl $ucl
     * @param array $paths
     * @param int $status
     * @param string|null $cancel
     * @param string|null $quit
     */
    public function __construct(
        Ucl $ucl,
        array $paths = [],
        int $status = Context::CREATED,
        string $cancel = null,
        string $quit = null
    )
    {
        $this->ucl = $ucl;
        $this->paths = $paths;
        $this->status = $status;
        $this->cancel = $cancel;
        $this->quit = $quit;
    }


    public function getId(): string
    {
        return $this->ucl->getContextId();
    }

    public function getUcl(): Ucl
    {
        return $this->ucl;
    }

    public function isStatus(int $statusCode): bool
    {
        return $this->status & $statusCode > 0;
    }


    public function addPaths(array $stages): void
    {
        $this->paths = array_merge($this->paths, $stages);
    }

    public function setPaths(array $stages): void
    {
        $this->paths = $stages;
    }

    public function insertPaths(array $stages): void
    {
        array_unshift($this->paths, ...$stages);
    }

    public function popPath(): ? Ucl
    {
        while($path = array_shift($this->paths)) {
            if ($path !== $this->ucl->stageName) {
                return $this->ucl->goStage($path);
            }
        }

        return null;
    }

    public function onCancel(string $stage = null): void
    {
        $this->cancel = $stage;
    }

    public function watchCancel(): ? Ucl
    {
        return isset($this->cancel)
            ? $this->ucl->goStage($this->cancel)
            : null;
    }

    public function onQuit(string $stage = null): void
    {
        $this->quit = $stage;
    }

    public function watchQuit(): ? Ucl
    {
        return isset($this->quit)
            ? $this->ucl->goStage($this->quit)
            : null;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getPaths(): array
    {
        return $this->paths;
    }

    public function setStatus(Ucl $ucl, int $status) : void
    {
        $this->ucl = $ucl;
        $this->status = $status;
    }

    public function toArray(): array
    {
        return [
            'ucl' => $this->ucl->encode(),
            'paths' => $this->paths,
            'status' => $this->status,
            'cancel' => $this->cancel,
            'quit' => $this->quit
        ];
    }


}