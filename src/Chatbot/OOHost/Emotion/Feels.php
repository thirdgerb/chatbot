<?php


namespace Commune\Chatbot\OOHost\Emotion;


use Commune\Chatbot\App\Messages\QA\Confirmation;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Emotion\Emotions\Negative;
use Commune\Chatbot\OOHost\Emotion\Emotions\Positive;
use Commune\Chatbot\OOHost\Session\Session;

/**
 * todo 情绪目前的实现策略还嫌太过复杂了. 未来考虑更改.
 */
class Feels implements Feeling
{
    /**
     * @var array
     */
    protected $intentMap = [];

    /**
     * @var callable[] string => callable
     */
    protected $experiences = [];

    public function __construct()
    {
        // 系统默认的两种情绪.
        $this->experience(Negative::class, function(Message $message) : bool {
            return $message instanceof Confirmation && $message->hasChoice(0);
        });

        $this->experience(Positive::class, function(Message $message) : bool {
            return $message instanceof Confirmation && $message->hasChoice(1);
        });
    }

    public function feel(Session $session, string $emotionName): bool
    {

        if (!is_a($emotionName, Emotion::class, TRUE)) {
            throw new ConfigureException(
                "emotion name $emotionName is not subclass of "
                . Emotion::class
            );
        }

        // NLU 正确解析了.
        if ($session->incomingMessage->getEmotions()->contains($emotionName)) {
            return true;
        }

        // 如果对象是emotion的实例
        $message = $session->incomingMessage->getMessage();
        if ($message instanceof Emotion) {
            return is_a($message, $emotionName, TRUE);
        }

        $intent = $message instanceof IntentMessage
            ? $message
            : $session->getMatchedIntent();

        // 如果意图存在的话.
        if (isset($intent)) {

            // 实例
            if (is_a($intent, $emotionName, TRUE)) {
                return true;
            }

            // 注册过map
            if (array_key_exists($emotionName, $this->intentMap)) {

                $map = $this->intentMap[$emotionName];
                return in_array(get_class($intent), $map)
                    || in_array($intent->getName(), $map);
            }
        }

        // 如果注册了经验:
        if (isset($this->experiences[$emotionName])) {
            foreach ($this->experiences[$emotionName] as $validator) {
                $result = call_user_func($validator, $message);
                if (is_bool($result)) {
                    return $result;
                }
            }
        }

        // null 大部分时候当false 用.
        return null;
    }

    public function experience(string $emotionName, $experience): void
    {
        if (!is_a($emotionName, Emotion::class, TRUE)) {
            throw new \InvalidArgumentException(
                __METHOD__
                . ' emotionName must be subclass of ' . Emotion::class
            );
        }


        if( is_callable($experience)) {
            $this->experiences[$emotionName][] = $experience;

        } elseif (is_string($experience)) {
            $this->intentMap[$emotionName][] = $experience;

        } else {
            throw new \InvalidArgumentException(
                __METHOD__
                . ' experience should only be callable or intent name string'
            );
        }
    }

    public function setIntentMap(string $emotionName, array $intentNames): void
    {
        $this->intentMap[$emotionName] = $intentNames;
    }


}