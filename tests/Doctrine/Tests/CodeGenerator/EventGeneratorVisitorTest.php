<?php

namespace Doctrine\Tests\CodeGenerator;

use Doctrine\CodeGenerator\Builder\ClassBuilder;
use Doctrine\CodeGenerator\Builder\MethodBuilder;
use Doctrine\CodeGenerator\Builder\PropertyBuilder;
use Doctrine\CodeGenerator\GenerationProject;
use Doctrine\CodeGenerator\GeneratorEvent;
use Doctrine\CodeGenerator\EventGeneratorVisitor;

class EventGeneratorVisitorTest extends \PHPUnit_Framework_TestCase
{
    public function testVisitClass()
    {
        $evm     = $this->getMock('Doctrine\Common\EventManager');
        $project = new GenerationProject();
        $visitor = new EventGeneratorVisitor($evm, $project);

        $class = new ClassBuilder('stdClass');
        $evm->expects($this->once())->method('dispatchEvent')->with('onGenerateClass', new GeneratorEvent($class, $project));

        $visitor->visitClass($class);
        $visitor->visitClass($class);

        $this->assertEquals(1, count($visitor));
    }

    public function testVisitMethod()
    {
        $evm     = $this->getMock('Doctrine\Common\EventManager');
        $project = new GenerationProject();
        $visitor = new EventGeneratorVisitor($evm, $project);

        $method = new MethodBuilder('stdMethod', new ClassBuilder('foo'));
        $evm->expects($this->once())->method('dispatchEvent')->with('onGenerateMethod', new GeneratorEvent($method, $project));

        $visitor->visitMethod($method);
        $visitor->visitMethod($method);

        $this->assertEquals(1, count($visitor));
    }

    /**
     * @dataProvider dataVisitSpecialMethod
     */
    public function testVisitSpecialMethod($method, $expectedEventName)
    {
        $evm     = $this->getMock('Doctrine\Common\EventManager');
        $project = new GenerationProject();
        $visitor = new EventGeneratorVisitor($evm, $project);

        $method = new MethodBuilder($method, new ClassBuilder('foo'));
        $evm->expects($this->once())->method('dispatchEvent')->with($expectedEventName, new GeneratorEvent($method, $project));

        $visitor->visitMethod($method);
    }

    public function dataVisitSpecialMethod()
    {
        return array(
            array('setFoo', 'onGenerateSetter'),
            array('getFoo', 'onGenerateGetter'),
            array('__construct', 'onGenerateConstructor'),
        );
    }

    public function testVisitProperty()
    {
        $evm     = $this->getMock('Doctrine\Common\EventManager');
        $project = new GenerationProject();
        $visitor = new EventGeneratorVisitor($evm, $project);

        $property = new PropertyBuilder('stdProperty', new ClassBuilder('foo'));
        $evm->expects($this->once())->method('dispatchEvent')->with('onGenerateProperty', new GeneratorEvent($property, $project));

        $visitor->visitProperty($property);
        $visitor->visitProperty($property);

        $this->assertEquals(1, count($visitor));
    }
}
