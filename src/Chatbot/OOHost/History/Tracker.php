<?php


namespace Commune\Chatbot\OOHost\History;

class Tracker
{
    /**
     * @var string
     */
    public $sessionId;

    public $tracking = [];

    /**
     * Tracker constructor.
     * @param string $sessionId
     */
    public function __construct(string $sessionId)
    {
        $this->sessionId = $sessionId;
    }


    public function log(string $navigation, Node $task)
    {
        $this->tracking[] = [
            'run' => $navigation,
            'name' => $task->getContextName(),
            'id' => $task->getContextId(),
            'stage' => $task->getStage(),
        ];
    }

    public function __toString() : string
    {
        return implode("\n=>", array_map(function(array $i) {
            $str = '';
            foreach ($i as $key => $value) {
                $str.= "$key:$value;";
            }
            return $str;
        }, $this->tracking));
    }
}