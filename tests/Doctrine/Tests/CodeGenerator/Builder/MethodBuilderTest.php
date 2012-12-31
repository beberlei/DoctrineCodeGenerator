<?php

namespace Doctrine\Tests\CodeGenerator\Builder;

use Doctrine\CodeGenerator\Builder\MethodBuilder;
use Doctrine\CodeGenerator\Builder\ClassBuilder;

class MethodBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $builder = new MethodBuilder("test", new ClassBuilder("foo"));

        $this->assertEquals("test", $builder->getName());
    }

    public function testGetClass()
    {
        $builder = new MethodBuilder("test", $class= new ClassBuilder("foo"));

        $this->assertSame($class, $builder->getClass());
    }

    public function testGetNode()
    {
        $builder = new MethodBuilder("test", new ClassBuilder("foo"));
        $builder->param('foo');

        $node = $builder->getNode();

        $this->assertInstanceOf('PHPParser_Node_Stmt_ClassMethod', $node);
        $this->assertEquals('test', $node->name);
        $this->assertEquals('foo', $node->params[0]->name);
    }

    public function testVisit()
    {
        $builder = new MethodBuilder("test", new ClassBuilder("foo"));
        $visitor = $this->getMock('Doctrine\CodeGenerator\Builder\Visitor');

        $visitor->expects($this->once())->method('visitMethod')->with($builder);

        $builder->visit($visitor);
    }
}
