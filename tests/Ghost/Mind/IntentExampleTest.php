<?php


namespace Commune\Test\Ghost\Mind;


use PHPUnit\Framework\TestCase;
use Commune\Ghost\IMindDef\Intent\IIntentExample;

class IntentExampleTest extends TestCase
{

    public function testParse()
    {
        $e = new IIntentExample(
            '[中文](lang)和english[还有if](and)数字比如[123](num)夹杂在一起'
        );

        $this->assertEquals(
            '中文和english还有if数字比如123夹杂在一起',
            $e->getText()
        );

        foreach ($e->getEntities() as $entity) {
            $this->assertEquals(
                mb_substr($e->getText(), $entity->start, $entity->width),
                $entity->value
            );
        }

        $this->assertEquals(
            [
                '中文',
                '还有if',
                '123',
            ],
            array_map(function($i){
                return $i->value;
            }, $e->getEntities())
        );

        $this->assertEquals(
            [
                'lang',
                'and',
                'num',
            ],
            array_map(function($i) {
                return $i->name;
            }, $e->getEntities())
        );
    }

    public function testEntitiesPattern()
    {
        $e = new IIntentExample(
            '[中文](lang)和english[还有if](and)数字比如[123](num)夹杂在一起'
        );

        $line = '';
        $right = '';
        foreach ($e->getEntities() as $entity) {
            $line .= $entity->left . $entity->value;
            $right = $entity->right;
        }
        $line .= $right;
        $this->assertEquals($e->getText(), $line);


        $line = '';
        $left = null;
        foreach ($e->getEntities() as $entity) {
            $left = $left ?? $entity->left;
            $line .= $entity->value .$entity->right;
        }
        $line = $left . $line;
        $this->assertEquals($e->getText(), $line);
    }

}