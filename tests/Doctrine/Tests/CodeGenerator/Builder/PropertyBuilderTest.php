<?php

namespace Doctrine\Tests\CodeGenerator\Builder;

use Doctrine\CodeGenerator\Builder\PropertyBuilder;
use Doctrine\CodeGenerator\Builder\ClassBuilder;

class PropertyBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $builder = new PropertyBuilder("foo", new ClassBuilder("Bar"));

        $this->assertEquals("foo", $builder->getName());
    }

    public function testGetClass()
    {
        $builder = new PropertyBuilder("foo", $class = new ClassBuilder("Bar"));

        $this->assertSame($class, $builder->getClass());
    }

    public function testSetGetAttribute()
    {
        $builder = new PropertyBuilder("foo", $class = new ClassBuilder("Bar"));

        $this->assertNull($builder->getAttribute('foo'));
        $builder->setAttribute('foo', 'bar');
        $this->assertEquals('bar', $builder->getAttribute('foo'));
    }

    public function testSetGetDocComment()
    {
        $builder = new PropertyBuilder("foo", $class = new ClassBuilder("Bar"));

        $this->assertNull($builder->getDocComment());
        $builder->setDocComment('bar');
        $this->assertEquals('bar', $builder->getDocComment());
    }

    public function testGetNode()
    {
        $builder = new PropertyBuilder("foo", new ClassBuilder("Bar"));
        $builder->makePublic()->makeStatic()->setDefault(false);

        $node = $builder->getNode();

        $this->assertInstanceOf('PHPParser_Node_Stmt_Property', $node);
        $this->assertEquals('foo', $node->props[0]->name);
        $this->assertEquals('false', $node->props[0]->default->name->parts[0]);
    }
}
