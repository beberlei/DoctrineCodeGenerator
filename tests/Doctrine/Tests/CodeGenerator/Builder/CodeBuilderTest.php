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

    public function testAssignment()
    {
        $foo = $this->builder->variable('foo');
        $bar = $this->builder->variable('bar');
        $stmt = $this->builder->assignment($foo, $bar);

        $this->assertInstanceOf('PHPParser_Node_Expr_Assign', $stmt);
        $this->assertSame($stmt->var, $foo);
        $this->assertSame($stmt->expr, $bar);
    }

    public function testInstantiate()
    {
        $expr = $this->builder->instantiate('DateTime');

        $this->assertInstanceOf('PHPParser_Node_Expr_New', $expr);
    }

    public function testCode()
    {
        $exprs = $this->builder->code('$foo');

        $this->assertEquals(1, count($exprs));
        $this->assertInstanceOf('PHPParser_Node_Expr_Variable', $exprs[0]);
        $this->assertEquals('foo', $exprs[0]->name);
    }

    public function testClassCode()
    {
        $exprs = $this->builder->classCode(<<<PHP
        private \$foo;

        public function foo() {}
PHP
        );

        $this->assertEquals(2, count($exprs));
        $this->assertInstanceOf('PHPParser_Node_Stmt_Property', $exprs[0]);
        $this->assertInstanceOf('PHPParser_Node_Stmt_ClassMethod', $exprs[1]);
    }

    public function testClassBuilder()
    {
        $builder = $this->builder->classBuilder('Foo');

        $this->assertInstanceOf('Doctrine\CodeGenerator\Builder\ClassBuilder', $builder);
        $this->assertEquals('Foo', $builder->getNode()->name);
    }
}

