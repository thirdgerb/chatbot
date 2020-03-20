<?php


namespace Commune\Components\UnheardLike\Libraries;


use Commune\Components\UnheardLike\Options\Character;
use Commune\Components\UnheardLike\Options\Episode;
use Commune\Components\UnheardLike\Options\Frame;

class FrameBuilder
{
    /**
     * @var Episode
     */
    protected $episode;

    /**
     * @var Frame[]
     */
    protected $frames = [];

    /**
     * FrameBuilder constructor.
     * @param Episode $episode
     */
    public function __construct(Episode $episode)
    {
        $this->episode = $episode;
        $this->build();
    }


    /**
     * @return Frame[]
     */
    public function toFrames() : array
    {
        return $this->frames;
    }

    protected function build() : void
    {
        // 先把第一帧创建出来.
        $this->getFrame($this->episode->initialize->time);

        // 用旁白作为锚点
        foreach ($this->episode->aside as $action) {
            $frame = $this->getFrame($action->t);
            foreach ($action->lines as $line) {
                $frame->addLineToRoom($action->at, $line);
            }
        }
        $characters = $this->episode->characters;

        foreach ($characters as $character) {
            $this->mergeCharacter($character);
        }

        ksort($this->frames);

        $result = [];

        $last = array_shift($this->frames);
        while($current = array_shift($this->frames)) {
            /**
             * @var Frame $last
             * @var Frame $current
             */
            $result[$last->time] = $last;
            $last->setNextFrame($current->time);

            // 同步上一帧的位置. 除非有新位置.
            foreach ($last->roleRooms as $role => $room) {
                if (! $current->hasRoleRoom($role) && isset($room)) {
                    $current->setRoleRoom($role, $room);
                }
            }

            $last = $current;
        }

        // 补一下尾部
        $result[$last->time] = $last;
        $this->frames = $result;
    }


    protected function mergeCharacter(Character $character) : void
    {
        $id = $character->id;
        foreach ($character->timeline as $action) {
            $frame = $this->getFrame($action->t);
            $at = $action->at;
            $frame->setRoleRoom($id, $at);
            foreach ($action->lines as $line) {
                $frame->addLineToRoom($at, $line);
            }
        }
    }

    protected function getFrame(string $time) : Frame
    {
        return $this->frames[$time]
            ?? $this->frames[$time] = Frame::createById($time, [
                'roleRooms' => array_fill_keys($this->episode->getRoleIds(), null),
            ]);
    }
}