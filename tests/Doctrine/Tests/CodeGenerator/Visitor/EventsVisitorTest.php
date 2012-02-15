<?php
namespace Doctrine\Tests\CodeGenerator\Visitor;

use Doctrine\CodeGenerator\Visitor\EventsVisitor;

class EventsVisitorTest extends \PHPUnit_Framework_TestCase
{
    private $evm;
    private $visitor;

    public function setUp()
    {
        $this->evm = $this->getMock('Doctrine\Common\EventManager', array('dispatchEvent'));
        $this->visitor = new EventsVisitor($this->evm);
    }

    public function testEnterNonTriggeredNode()
    {
        $this->evm->expects($this->never())->method('dispatchEvent');

        $expr = new \PHPParser_Node_Expr_FuncCall('phpinfo');
        $this->visitor->enterNode($expr);
    }

    public function testEnterNode()
    {
        $this->evm->expects($this->once())->method('dispatchEvent')
                  ->with($this->equalTo('onGenerateClass'));

        $node = new \PHPParser_Node_Stmt_Class('stdClass');
        $this->visitor->enterNode($node);
    }

    public function testNeverTriggerSameNodeTwice()
    {
        $this->evm->expects($this->once())->method('dispatchEvent')
                  ->with($this->equalTo('onGenerateClass'));

        $node = new \PHPParser_Node_Stmt_Class('stdClass');

        $this->visitor->enterNode($node);
        $this->visitor->enterNode($node);
    }

    /**
     * @dataProvider dataMethodsEvents
     */
    public function testTriggerClassMethodSpecialCases($methodName, $expectedEvent)
    {
        $this->evm->expects($this->once())->method('dispatchEvent')
                  ->with($this->equalTo($expectedEvent));

        $node = new \PHPParser_Node_Stmt_ClassMethod($methodName);

        $this->visitor->enterNode($node);
    }

    static public function dataMethodsEvents()
    {
        return array(
            array('setFoo', 'onGenerateSetter'),
            array('getFoo', 'onGenerateGetter'),
            array('__construct', 'onGenerateConstructor'),
            array('doSomething', 'onGenerateMethod'),
        );
    }
}

