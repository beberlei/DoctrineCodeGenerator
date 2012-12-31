<?php
namespace Doctrine\Tests\CodeGenerator\Listener;

use Doctrine\CodeGenerator\Listener\TimestampableListener;
use Doctrine\CodeGenerator\Builder\CodeBuilder;
use Doctrine\CodeGenerator\Builder\ClassBuilder;

class TimestampableListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testMakeClassTimestampable()
    {
        $class       = new ClassBuilder('Foo');
        $listener    = new TimestampableListener();

        $listener->makeTimestampable($class);
    }

    public function testMakeClassTimestampableExistingConstructor()
    {
        $codeBuilder = new CodeBuilder();
        $class       = new ClassBuilder('Foo');
        $constructor = $class->getMethod('__construct');
        $constructor->append(array(
            $codeBuilder->assignment(
                $codeBuilder->instanceVariable('foo'),
                $codeBuilder->variable('bar')
            )
        ));

        $listener    = new TimestampableListener();

        $listener->makeTimestampable($class);

        $node = $constructor->getNode();

        $printer = new \PHPParser_PrettyPrinter_Zend();

        $this->assertEquals(2, count($node->stmts));
        $this->assertEquals(<<<'PHP'
public function __construct()
{
    $this->foo = $bar;
    $this->created = $this->updated = new \DateTime();
}
PHP
        , $printer->prettyPrint(array($node)));
    }
}
