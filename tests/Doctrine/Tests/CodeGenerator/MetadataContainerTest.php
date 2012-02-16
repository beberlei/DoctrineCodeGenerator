<?php

namespace Doctrine\Tests\CodeGenerator;

use Doctrine\CodeGenerator\MetadataContainer;

class MetadataContainerTest extends \PHPUnit_Framework_TestCase
{
    private $container;

    public function setUp()
    {
        $this->container = new MetadataContainer();
    }

    public function testTags()
    {
        $node = $this->getMock('PHPParser_Node');

        $this->assertFalse($this->container->hasTag($node, 'entity'));
        $this->assertFalse($this->container->hasTag($node, 'controller'));

        $this->container->addTag($node, 'entity');

        $this->assertTrue($this->container->hasTag($node, 'entity'));
        $this->assertFalse($this->container->hasTag($node, 'controller'));
    }

    public function testAttribute()
    {
        $node = $this->getMock('PHPParser_Node');

        $this->assertNull($this->container->getAttribute($node, 'type'));

        $this->container->setAttribute($node, 'type', 'integer');

        $this->assertEquals('integer', $this->container->getAttribute($node, 'type'));
        $this->assertNull($this->container->getAttribute($node, 'foobar'));
    }
}

