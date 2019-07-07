<?php


namespace Commune\Chatbot\App\Components\Configurable\Resources;

use Commune\Chatbot\App\Components\Configurable\Configs\DomainConfig;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\App\Contexts\Restful\ResourceDef;
use Commune\Chatbot\App\Components\Configurable\Drivers\DomainConfigRepository;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Support\Option;
use Illuminate\Support\Arr;

/**
 * @property DomainConfig $domainConfig
 */
abstract class AbsDomainResource extends ResourceDef
{
    /*------- 必须定义的配置. -------*/

    // 资源的所在域. 必填. 没有 '.' 的字符串.
    const MODULE = '';

    // 资源对应的key, 有先后顺序, 最后一个是自己.
    const PATHS = [];

    // 资源自己 ID 的名称.
    const IDENTITY = '';

    const DESCRIPTION = '';

    /**
     * @var DomainConfigRepository
     */
    protected $domainRepo;

    protected function getRepo(Dialog $dialog) : DomainConfigRepository
    {
        return $this->domainRepo
            ?? $this->domainRepo = $dialog->app->make(DomainConfigRepository::class);
    }

    function isListExists(Dialog $dialog, array $paths): bool
    {
        //repo
        if (empty($paths)) {
            return true;
        }
        return $this->isResourceExists($dialog, $paths, null);
    }

    function isResourceExists(Dialog $dialog, array $paths, $id): bool
    {
        $option = $this->fetchResource($dialog, $paths, $id);
        return isset($option);
    }

    /**
     * @param Dialog $dialog
     * @param string $domainName
     * @return DomainConfig|null
     */
    protected function fetchDomain(Dialog $dialog, $domainName) : ? DomainConfig
    {
        $repo = $this->getRepo($dialog);

        if (!$repo->has((string) $domainName)) {
            return null;
        }

        return $repo->get($domainName);
    }

    /**
     * @param Dialog $dialog
     * @param array $paths
     * @param null $id
     * @return array|null [Option $option, string $dotKey, DomainConfig $domain]
     */
    protected function fetchResourceWithKey(Dialog $dialog, array $paths, $id =null) : ? array
    {
        if (isset($id)) {
            $paths[static::IDENTITY] = $id;
        }
        $domainName = array_shift($paths);

        $domain = $this->fetchDomain($dialog, $domainName);
        $option = $domain;

        $dotKey = '';
        foreach ($paths as $key => $value) {
            $dotKey .= ".$key";
            if (!$option->isListAssociation($key)) {
                return null;
            }

            $break = false;
            foreach ($option->{$key} as $index => $association) {
                /**
                 * @var Option $association
                 */
                if ($association->getId() == $value) {
                    // 找到了当前的option
                    $option = $association;
                    $dotKey.= ".$index";
                    $break = true;
                    break;
                }
            }

            if ($break) {
                break;
            }

            // 找不到值的话, 就是false
            return null;
        }

        return [$option, trim($dotKey, '.'), $domain];
    }

    protected function fetchResource(Dialog $dialog, array $paths, $id = null) : ? Option
    {
        $result = $this->fetchResourceWithKey($dialog, $paths, $id);
        if (!isset($result)) {
            return null;
        }
        list($option, $dotKey) = $result;
        return $option;
    }

    /**
     * @param Option $option
     * @return string
     */
    abstract public function describeResource($option) : string;

    function paginate(Dialog $dialog, array $paths, int $offset, int $limit): array
    {
        if (empty($paths)) {
            $repo = $this->getRepo($dialog);
            $items = $repo->each();

        } else {

            $option = $this->fetchResource($dialog, $paths, null);
            $key = static::IDENTITY;
            $items = $option->{$key};
        }

        $results = [];
        $i = 0;
        $end = $offset + $limit;
        foreach ($items as $item) {
            /**
             * @var Option $item
             */
            if ($i >= $offset) {
                $results[$item->getId()] = $this->describeResource($item);
            }

            if ($i >= $end) {
                break;
            }
        }
        return $results;
    }


    function listTotal(Dialog $dialog, array $paths): int
    {
        if (empty($paths))  {
            return $this->getRepo($dialog)->getCount();
        }
        $option = $this->fetchResource($dialog, $paths, null);
        $key = static::IDENTITY;
        return count($option->{$key});
    }


    function createResource(Dialog $dialog, array $paths, $id): ? string
    {
        if (empty($paths)) {
            $this->getRepo($dialog)->newDomain($id);
            return null;
        }
        list($option, $dotKey, $domain) = $this->fetchResourceWithKey($dialog, $paths, null);
        $optionClass = $option->getAssociationClass(static::IDENTITY);

        if (
            !is_string($optionClass)
            || !is_a($optionClass, Option::class, TRUE)
        ) {
            return '路径不正确, association 不是option 对象: ' . $optionClass;
        }

        $name = constant("$optionClass::IDENTITY");
        $item = [
            $name => $id
        ];



        //todo
        $data = $option->toArray();
        $key = implode('.', array_keys($paths));

        $items = Arr::get($data, $key);
        $items[] = $item;
        Arr::set($data, $key, $items);
        $this->domainConfig = new DomainConfig($data);
        return null;
    }


    function showResourceDetail(Dialog $dialog, array $paths, $id): string
    {
        $option = $this->fetchResource($dialog, $paths, $id);
        return $option->toPrettyJson();
    }


    function deleteResource(Dialog $dialog, array $paths, $id): ? string
    {
        if (empty($id)) {
            return 'id 不能为null';
        }

        if (empty($paths)) {
            $num = $this->getRepo($dialog)->remove($id);
            $dialog->say()->info("删除了 $num 条数据");
            return null;
        }

        $data = $this->fetchResourceWithKey($dialog, $paths, $id);

        if (!isset($data)) {
            return '资源不存在';
        }

        list($option, $dotKey, $domain) = $data;

        $keys = explode('.', $dotKey);
        $delKey = array_pop($keys);
        $parentKey = implode('.', $keys);

        $array = $domain->toArray();

        $modify = Arr::get($array, $parentKey);
        unset($modify[$delKey]);
        Arr::set($array, $parentKey, $modify);

        $config = new DomainConfig($array);
        $this->getRepo($dialog)->update($config);
        return null;
    }

    function saveResource(Dialog $dialog, array $paths, $id): ? string
    {
        $this->getRepo($dialog)->save();
        return null;
    }


    public function getResourceMenu(): array
    {
        $menu = $this->getDomainResourceMenu();
        $data = parent::getResourceMenu();
        return $menu + $data;
    }

    public function getDomainResourceMenu() : array
    {
        return [];
    }

    public function resourceView(Dialog $dialog) : ? Navigator
    {
        $resource = $this->fetchResource($dialog, $this->paths, $this->id);
        if (empty($resource)) {
            $dialog->say()->error('数据未找到. id: '.$this->id);
            $this->errCode = static::ERR_NOT_FOUND;
            return $dialog->fulfill();
        }

        $properties = $resource->getProperties();

        $info = 'Option 对象, 类名: '.get_class($resource);
        $info .= "\n 具体结构如下: \n";
        foreach ($properties as list($name, $type, $desc)) {
            $info .= "\n [$name] ($type) : $desc";
        }

        $dialog->say()->info($info);
        return null;
    }

    public function changeResource(Dialog $dialog, string $key, $value) : ? string
    {
        if ($this->isList) {
            return '列表不能修改值';
        }

        $result = $this->fetchResourceWithKey($dialog, $this->paths, $this->id);
        if (empty($result)) {
            return 'option 没有找到';
        }

        /**
         * @var Option $option
         * @var string $dotKey
         * @var DomainConfig $domain
         */
        list($option, $dotKey, $domain) = $result;

        $data = $domain->toArray();

        $realKey = trim("$dotKey.$key", '.');
        Arr::set($data, $realKey, $value);

        $updated = new DomainConfig($data);
        $this->getRepo($dialog)->update($updated);
        return null;
    }


    protected function doChangeVerbose(Stage $stage, string $key) : Navigator
    {
        return $stage
            ->onStart(function(Dialog $dialog) use ($key){
                $option = $this->fetchResource($dialog, $this->paths, $this->id);
                $value = json_encode(
                    $option->{$key},
                    JSON_PRETTY_PRINT
                    | JSON_UNESCAPED_SLASHES
                    |JSON_UNESCAPED_UNICODE
                );
                $dialog->say()->info("$key 的当前值为 : $value");
            })
            ->buildTalk()
            ->askVerbose("请输入 $key 的值: ")
            ->wait()
            ->hearing()
            ->isAnswer(function(Dialog $dialog, Answer $answer) use ($key){
                $r = $answer->toResult();
                $error = $this->changeResource($dialog, $key, $r);
                $this->checkResult($dialog, $error);
                return $dialog->restart();
            })
            ->end();
    }
}