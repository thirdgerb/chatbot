<?php


namespace Commune\Test\Chatbot\OOHost\Intent;


use Commune\Chatbot\OOHost\Context\Intent\NLUExample;
use PHPUnit\Framework\TestCase;

class NLUExampleTest extends TestCase
{

    public function testParse()
    {
        $e = new NLUExample(
            'test',
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

}