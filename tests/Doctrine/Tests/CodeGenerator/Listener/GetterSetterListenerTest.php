<?php

namespace Doctrine\Tests\CodeGenerator\Listener;

use Doctrine\CodeGenerator\GenerationProject;
use Doctrine\CodeGenerator\GeneratorEvent;
use Doctrine\CodeGenerator\Builder\PropertyBuilder;
use Doctrine\CodeGenerator\Builder\ClassBuilder;
use Doctrine\CodeGenerator\Listener\GetterSetterListener;

class GetterSetterListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testOnGenerateProperty()
    {
        $project  = new GenerationProject();
        $builder  = new PropertyBuilder('foo', $class = new ClassBuilder('Foo'));
        $listener = new GetterSetterListener();

        $listener->onGenerateProperty(new GeneratorEvent($builder, $project));

        $node = $class->getNode();
        $printer = new \PHPParser_PrettyPrinter_Zend();

        $this->assertEquals(<<<'PHP'
class Foo
{
    public function setFoo($foo)
    {
        $this->foo = $foo;
    }
    public function getFoo()
    {
        return $this->foo;
    }
}
PHP
            , $printer->prettyPrint(array($node)));
    }
}
