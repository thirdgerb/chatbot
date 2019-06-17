<?php


namespace Commune\Chatbot\App\Messages\QA;

class Confirmation extends VbAnswer
{
    /**
     * @return bool
     */
    public function toResult()
    {
        return $this->choice == 0 ;
    }

}