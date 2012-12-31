<?php

namespace Doctrine\Tests\CodeGenerator\Listener;

use Doctrine\CodeGenerator\Listener\FluentSetterListener;
use Doctrine\CodeGenerator\GeneratorEvent;
use Doctrine\CodeGenerator\GenerationProject;
use Doctrine\CodeGenerator\Builder\MethodBuilder;
use Doctrine\CodeGenerator\Builder\ClassBuilder;

class FluentSetterListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testOnGenerateSetter()
    {
        $project = new GenerationProject();
        $builder = new MethodBuilder("setFoo", new ClassBuilder("Foo"));
        $listener = new FluentSetterListener();

        $listener->onGenerateSetter(new GeneratorEvent($builder, $project));

        $node    = $builder->getNode();
        $printer = new \PHPParser_PrettyPrinter_Zend();

        $this->assertEquals(<<<'PHP'
public function setFoo()
{
    return $this;
}
PHP
        , $printer->prettyPrint(array($node)));
    }
}
