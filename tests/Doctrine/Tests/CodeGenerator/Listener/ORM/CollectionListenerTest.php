<?php

namespace Doctrine\Tests\CodeGenerator\Listener\ORM;

use Doctrine\CodeGenerator\Listener\ORM\CollectionListener;
use Doctrine\CodeGenerator\GeneratorEvent;
use Doctrine\CodeGenerator\GenerationProject;
use Doctrine\CodeGenerator\Builder\ClassBuilder;
use Doctrine\CodeGenerator\Builder\PropertyBuilder;

class CollectionListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testOnGenerateProperty()
    {
        $builder  = new PropertyBuilder('foo', new ClassBuilder('Foo'));
        $builder->setAttribute('isAssociation', true);
        $builder->setAttribute('mapping', array(
            'targetEntity' => 'stdClass',
            'type' => \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY
        ));

        $project  = new GenerationProject();
        $listener = new CollectionListener();

        $listener->onGenerateProperty(new GeneratorEvent($builder, $project));

        $class = $builder->getClass();

        $this->assertTrue($class->hasMethod('addFoo'));
        $this->assertTrue($class->hasMethod('removeFoo'));

        $printer = new \PHPParser_PrettyPrinter_Zend();
        $this->assertEquals(<<<'PHP'
class Foo
{
    public function __construct()
    {
        $this->foo = new \Doctrine\Common\Collections\ArrayCollection();
    }
    public function addFoo(stdClass $foo)
    {
        $this->foo[] = $foo;
    }
    public function removeFoo(stdClass $foo)
    {
        $this->foo->removeElement($foo);
    }
}
PHP
            , $printer->prettyPrint(array($class->getNode())));
    }
}

