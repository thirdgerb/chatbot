<?php


namespace Commune\Chatbot\OOHost\Emotion;

use Commune\Chatbot\Blueprint\Message\VerbalMsg;
use Commune\Chatbot\Framework\Exceptions\ChatbotLogicException;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Emotion\Emotions\Negative;
use Commune\Chatbot\OOHost\Emotion\Emotions\Positive;
use Commune\Chatbot\OOHost\Session\Session;
use Illuminate\Contracts\Container\BindingResolutionException;

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
        $this->defaultExperience();
    }

    protected function defaultExperience() : void
    {
        // 系统默认的两种情绪.
        // positive
        $this->experience(Positive::class, function(Session $session) : bool {

            $message = $session->incomingMessage->getMessage();
            return $message instanceof VerbalMsg
                && $message->getTrimmedText() === 'y';
        });

        // negative
        $this->experience(Negative::class, function(Session $session) : bool {
            $message = $session->incomingMessage->getMessage();
            return $message instanceof VerbalMsg
                && $message->getTrimmedText() === 'n';
        });

    }

    /**
     * todo too expensive to feel undefined emotion
     *
     * @param Session $session
     * @param string $emotionName
     * @return bool
     */
    public function feel(Session $session, string $emotionName): bool
    {

        if (!is_a($emotionName, Emotion::class, TRUE)) {
            throw new ChatbotLogicException(
                "emotion name $emotionName is not subclass of "
                . Emotion::class
            );
        }

        // NLU 正确解析了.
        if ($session->nlu->getEmotions()->contains($emotionName)) {
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

            // intent 本身就是 emotion 的实例
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

        try {
            // 如果注册了经验:
            if (isset($this->experiences[$emotionName])) {
                foreach ($this->experiences[$emotionName] as $validator) {

                    $result = call_user_func($validator, $session);

                    if ($result === true) {
                        return $result;
                    }
                }
            }
        } catch (BindingResolutionException $e) {
            $session->logger->error($e);
        } catch (\ReflectionException $e) {
            $session->logger->error($e);
        }

        return false;
    }

    public function experience(string $emotionName, callable $experience): void
    {
        if (!is_a($emotionName, Emotion::class, TRUE)) {
            throw new \InvalidArgumentException(
                __METHOD__
                . ' emotionName must be subclass of ' . Emotion::class
            );
        }

        $this->experiences[$emotionName][] = $experience;
    }

    public function setIntentMap(string $emotionName, array $intentNames): void
    {
        $this->intentMap[$emotionName] = $intentNames;
    }


}