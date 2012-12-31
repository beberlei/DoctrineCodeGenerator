<?php

namespace Doctrine\Tests\CodeGenerator\Builder;

use Doctrine\CodeGenerator\Builder\ClassBuilder;
use Doctrine\Tests\CodeGenerator\TestCase;

class ClassBuilderTest extends TestCase
{
    public function testGetNames()
    {
        $builder = new ClassBuilder("PHP\\stdClass");

        $this->assertEquals("PHP\\stdClass", $builder->getName());
        $this->assertEquals("PHP", $builder->getNamespace());
    }

    public function testGetNode()
    {
        $builder = new ClassBuilder("Foo");
        $builder->extend("stdClass")
                ->implement("Jsonable")
                ->makeAbstract();

        $node = $builder->getNode();

        $this->assertInstanceOf('PHPParser_Node_Stmt_Class', $node);
        $this->assertEquals("Foo", $node->name);
        $this->assertEquals("stdClass", (string)$node->extends);
        $this->assertEquals("Jsonable", (string)$node->implements[0]);
    }

    public function testMethodBuilder()
    {
        $builder = new ClassBuilder("Foo");

        $this->assertFalse($builder->hasMethod("test"));

        $method = $builder->getMethod("test");

        $this->assertInstanceOf('Doctrine\CodeGenerator\Builder\MethodBuilder', $method);
        $this->assertTrue($builder->hasMethod("test"));
        $this->assertSame($method, $builder->getMethod("test"));
    }

    public function testPropertyBuilder()
    {
        $builder = new ClassBuilder("Foo");

        $this->assertFalse($builder->hasProperty("test"));

        $property = $builder->getProperty("test");

        $this->assertInstanceOf('Doctrine\CodeGenerator\Builder\PropertyBuilder', $property);
        $this->assertTrue($builder->hasProperty("test"));
        $this->assertSame($property, $builder->getProperty("test"));
    }

    public function testVisit()
    {
        $builder = new ClassBuilder("Foo");
        $visitor = $this->getMock('Doctrine\CodeGenerator\Builder\Visitor');

        $builder->getProperty("test");
        $builder->getMethod("test");

        $visitor->expects($this->at(0))->method('visitClass');
        $visitor->expects($this->at(1))->method('visitProperty');
        $visitor->expects($this->at(2))->method('visitMethod');

        $builder->visit($visitor);
    }
}

