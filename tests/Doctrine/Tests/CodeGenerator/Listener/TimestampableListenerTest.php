<?php
namespace Doctrine\Tests\CodeGenerator\Listener;

use Doctrine\CodeGenerator\Listener\TimestampableListener;
use Doctrine\CodeGenerator\Builder\CodeBuilder;
use Doctrine\CodeGenerator\Builder\Manipulator;
use PHPParser_Node_Stmt_Class;

class TimestampableListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testMakeClassTimestampable()
    {
        $this->markTestSkipped();
        $class       = new PHPParser_Node_Stmt_Class("Test");
        $codeBuilder = new CodeBuilder();
        $listener    = new TimestampableListener();

        $listener->makeTimestampable($class, $codeBuilder);

        $manipulator = new Manipulator();

        $this->assertTrue($manipulator->hasProperty($class, 'updated'));
        $this->assertTrue($manipulator->hasProperty($class, 'created'));
        $this->assertTrue($manipulator->hasMethod($class, 'getCreated'));
        $this->assertTrue($manipulator->hasMethod($class, 'getUpdated'));
        $this->assertTrue($manipulator->hasMethod($class, 'setUpdated'));
    }

    public function testMakeClassTimestampableExistingConstructor()
    {
        $this->markTestSkipped();
        $class       = new PHPParser_Node_Stmt_Class("Test");
        $codeBuilder = new CodeBuilder();
        $manipulator = new Manipulator();

        $manipulator->append(
            $manipulator->findMethod($class, '__construct'),
            $codeBuilder->code("\$this->foo = 'bar';")
        );

        $listener    = new TimestampableListener();

        $listener->makeTimestampable($class, $codeBuilder);

        $constructor = $manipulator->findMethod($class, '__construct');
        $this->assertEquals(2, count($constructor->stmts));
    }
}
