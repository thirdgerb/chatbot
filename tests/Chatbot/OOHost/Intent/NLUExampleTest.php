<?php


namespace Commune\Test\Chatbot\OOHost\Intent;


use Commune\Chatbot\OOHost\NLU\Corpus\Example as NLUExample;
use PHPUnit\Framework\TestCase;

class NLUExampleTest extends TestCase
{

    public function testParse()
    {
        $e = new NLUExample(
            '[中文](lang)和english[还有if](and)数字比如[123](num)夹杂在一起'
        );

        $this->assertEquals(
            '中文和english还有if数字比如123夹杂在一起',
            $e->text
        );

        foreach ($e->getExampleEntities() as $entity) {
            $this->assertEquals(
                mb_substr($e->text, $entity->start, $entity->width),
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
            }, $e->getExampleEntities())
        );

        $this->assertEquals(
            [
                'lang',
                'and',
                'num',
            ],
            array_map(function($i) {
                return $i->name;
            }, $e->getExampleEntities())
        );
    }

    public function testEntitiesPattern()
    {
        $e = new NLUExample(
            '[中文](lang)和english[还有if](and)数字比如[123](num)夹杂在一起'
        );

        $line = '';
        $right = '';
        foreach ($e->entities as $entity) {
            $line .= $entity->left . $entity->value;
            $right = $entity->right;
        }
        $line .= $right;
        $this->assertEquals($e->text, $line);


        $line = '';
        $left = null;
        foreach ($e->entities as $entity) {
            $left = $left ?? $entity->left;
            $line .= $entity->value .$entity->right;
        }
        $line = $left . $line;
        $this->assertEquals($e->text, $line);
    }

}