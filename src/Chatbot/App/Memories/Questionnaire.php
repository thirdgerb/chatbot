<?php


namespace Commune\Chatbot\App\Memories;

use Commune\Chatbot\App\Messages\QA\Choice;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Session\Scope;
use Commune\Support\OptionRepo\Contracts\OptionRepository;
use Illuminate\Support\Str;


/**
 * 与记忆结合的问卷调查组件. 算是一个示例.
 * 用这个 context 可以快速实现定制好的问卷调查.
 *
 * 更好的实现办法, 是定义一个 QuestionnaireComponent
 * 在 @see OptionRepository 里用 Option 定义问卷
 * 然后用独立的 QuestionnaireRegistrar 生成问卷专用的 Definition
 *
 * @property bool $finish  问卷是否已经有结果.
 * @property array $answers  问题编号 => 答案
 * @property int $questionNumber 当前问题编号.
 * @property-read int $questionTotal 总题目数.
 */
abstract class Questionnaire extends MemorialTask
{
    /*------- 用户配置. -------*/

    const DESCRIPTION = "请撰写问题说明";

    // 记忆域. 默认用户级别的.
    const SCOPES = [Scope::USER_ID];

    /*------- 系统设置 -------*/

    // question stage 默认定义为 on_question_number;
    const QUESTION_STAGE_PREFIX = '_question_';

    /*------- 缓存参数 -------*/

    /**
     * @var int
     */
    protected $_total;


    protected function init() : array
    {
        return [
            'finish' => false,
            'answers' => [],
            'questionNumber' => 0
        ];
    }


    /**
     *
     * @return array   格式是  [
     *  '问题' => [
     *      '序号1' => '选项1',
     *      '序号2' => '选项2',
     *      '序号3' => '选项3',
     *      ],
     * ]
     */
    abstract public static function getQuestionDefinition() : array;

    /**
     * 用户回答结束时做的操作.
     *
     * @param Dialog $dialog
     * @return Navigator|null  返回为 null 时会直接退出.
     */
    abstract public function doFinal(Dialog $dialog) : ? Navigator;

    /**
     * 用户的回答如果不在选项内时, 默认应该怎么处理.
     * @return callable|null
     */
    abstract protected function defaultFallback() : ? callable ;

    /**
     * 每次回答完问题, 是否要做额外的处理.
     *
     * @param Dialog $dialog
     * @param int $questionIndex
     * @param $choice
     * @return Navigator|null
     */
    abstract protected function onAnswered(Dialog $dialog, int $questionIndex, $choice) : ? Navigator;

    /**
     * 拿到结果之后的处理.
     * @param Stage $stage
     * @return Navigator
     */
    public function __onFinal(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->interceptor([$this, 'doFinal'])
            ->action(function(Dialog $dialog) : Navigator{
                $this->finish = true;
                return $dialog->fulfill();
            });
    }


    protected function doAsk(Stage $stage) : Navigator
    {
        $definition = $this->getQuestionDefinition();
        $questions = array_keys($definition);
        $answers = array_values($definition);

        $index = $this->questionNumber;
        return $stage->buildTalk()
            ->askChoose(
                $this->wrapQuestion($questions[$index]),
                $this->wrapAnswers($answers[$index])
            )
            ->wait()
            ->hearing()
            ->isAnswer(function(Dialog $dialog, Choice $choice) {
                $answer = $choice->getChoice();
                $answers = $this->answers;
                $answers[$this->questionNumber] = $answer;
                $this->answers = $answers;

                // 可以在这里做一些特殊的处理.
                $navigator = $this->onAnswered($dialog, $this->questionNumber, $answer);
                // 否则按题目顺序前进.
                return $navigator ?? $this->next($dialog);
            })
            ->end($this->defaultFallback());
    }

    /**
     * 对问题进行格式化的包装.
     * @param string $question
     * @return string
     */
    protected function wrapQuestion(string $question) : string
    {
        $id = $this->questionNumber + 1;
        $total = $this->questionTotal;
        return "第 $id/$total 题: $question";
    }

    /**
     * 对选项进行统一封装
     * @param array $answers
     * @return array
     */
    protected function wrapAnswers(array $answers) : array
    {
        return $answers;
    }

    /**
     * 自动进入下一题. 不给index的话, 默认是题目编号加1
     *
     * @param Dialog $dialog
     * @param int|null $index
     * @return Navigator
     */
    protected function next(Dialog $dialog, $index = null) : Navigator
    {
        $index = $index ?? $this->questionNumber + 1;
        // 最后一题
        if ($index >= $this->questionTotal) {
            return $dialog->goStage('final');
        }
        return $dialog->goStage(static::QUESTION_STAGE_PREFIX . $index);
    }


    /**
     * questionTotal 的 getter 方法
     * @return int
     */
    public function __getQuestionTotal()
    {
        return $this->_total ?? $this->_total = count($this->getQuestionDefinition());
    }

    public static function buildDefinition(): Definition
    {
        $def = parent::buildDefinition();
        $questions = static::getQuestionDefinition();

        $i = 0;
        foreach ($questions as $answers) {

            $def->setStage(
                $stageName = static::QUESTION_STAGE_PREFIX.$i,
                function(Stage $stage) use ($stageName): Navigator {
                    return call_user_func(
                        [$stage->self, static::STAGE_METHOD_PREFIX . $stageName],
                        $stage
                    );
                }
            );
            $i++;
        }

        return $def;
    }

    /**
     * 用魔术方法来调用 question stage
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $method = static::STAGE_METHOD_PREFIX . static::QUESTION_STAGE_PREFIX;
        if (Str::startsWith($name, $method)) {
            $questionIndex = substr($name, strlen($method));
            $index = intval($questionIndex);
            $this->questionNumber = $index;
            return call_user_func_array([$this, 'doAsk'], $arguments);

        }

        throw new \BadMethodCallException("method $name not exists");
    }

}