<?php

namespace Doctrine\Tests\CodeGenerator\Builder;

use Doctrine\CodeGenerator\Builder\CodeBuilder;

class CodeBuilderTest extends \PHPUnit_Framework_TestCase
{
    private $builder;

    public function setUp()
    {
        $this->builder = new CodeBuilder();
    }

    public function testInstanceVariable()
    {
        $expr = $this->builder->instanceVariable('foo');

        $this->assertInstanceOf('PHPParser_Node_Expr_PropertyFetch', $expr);
        $this->assertInstanceOf('PHPParser_Node_Expr_Variable', $expr->var);
        $this->assertEquals('this', $expr->var->name);
        $this->assertEquals('foo', $expr->name);
    }

    public function testVariable()
    {
        $expr = $this->builder->variable('foo');

        $this->assertInstanceOf('PHPParser_Node_Expr_Variable', $expr);
        $this->assertEquals('foo', $expr->name);
    }

    public function testReturn()
    {
        $stmt = $this->builder->returnStmt($this->builder->variable('foo'));

        $this->assertInstanceOf('PHPParser_Node_Stmt_Return', $stmt);
    }

    public function testInstantiate()
    {
        $expr = $this->builder->instantiate('DateTime');

        $this->assertInstanceOf('PHPParser_Node_Expr_New', $expr);
    }
}

