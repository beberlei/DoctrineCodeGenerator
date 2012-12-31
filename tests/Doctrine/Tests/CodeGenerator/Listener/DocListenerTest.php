<?php

namespace Doctrine\Tests\CodeGenerator\Listener;

use Doctrine\CodeGenerator\GenerationProject;
use Doctrine\CodeGenerator\GeneratorEvent;
use Doctrine\CodeGenerator\Builder\PropertyBuilder;
use Doctrine\CodeGenerator\Builder\MethodBuilder;
use Doctrine\CodeGenerator\Builder\ClassBuilder;
use Doctrine\CodeGenerator\Listener\DocListener;

class DocListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testOnGenerateProperty()
    {
        $project  = new GenerationProject();
        $builder  = new PropertyBuilder("foo", new ClassBuilder("Foo"));
        $builder->setAttribute('type', 'DateTime');

        $listener = new DocListener();

        $listener->onGenerateProperty(new GeneratorEvent($builder, $project));

        $this->assertEquals(<<<PHP
/**
 * @var DateTime
 */
PHP
            , $builder->getDocComment());
    }

    public function testOnGenerateGetter()
    {
        $project  = new GenerationProject();
        $builder  = new MethodBuilder("getFoo", new ClassBuilder("Foo"));

        $listener = new DocListener();

        $listener->onGenerateGetter(new GeneratorEvent($builder, $project));

        $this->assertEquals(<<<PHP
/**
 * Return foo
 *
 * @return mixed
 */
PHP
            , $builder->getDocComment());
    }

    public function testOnGenerateSetter()
    {
        $project  = new GenerationProject();
        $builder  = new MethodBuilder("setFoo", new ClassBuilder("Foo"));

        $listener = new DocListener();

        $listener->onGenerateSetter(new GeneratorEvent($builder, $project));

        $this->assertEquals(<<<'PHP'
/**
 * Set foo
 *
 * @param mixed $foo
 */
PHP
            , $builder->getDocComment());
    }
}
