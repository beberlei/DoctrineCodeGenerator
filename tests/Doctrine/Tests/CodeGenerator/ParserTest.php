<?php

namespace Doctrine\Tests\CodeGenerator;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    private $parser;

    public function setUp()
    {
        $this->parser = new \Doctrine\CodeGenerator\Parser();
    }

    public function testParseString()
    {
        $ast = $this->parser->parseString('<?php phpinfo(); ?>');

        $this->assertEquals(1, count($ast));
        $this->assertInstanceOf('PHPParser_Node_Expr_FuncCall', $ast[0]);
    }

    public function testParseStringWithoutPhpBrackets()
    {
        $ast = $this->parser->parseString('phpinfo();');

        $this->assertEquals(1, count($ast));
        $this->assertInstanceOf('PHPParser_Node_Stmt_InlineHTML', $ast[0]);
    }

    public function testParseStringTraverseNodes()
    {
        $visitor = $this->getMock('PHPParser_NodeVisitor');
        $visitor->expects($this->once())->method('enterNode');
        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor($visitor);

        $this->parser = new \Doctrine\CodeGenerator\Parser($traverser);
        $ast = $this->parser->parseString('phpinfo();');
    }

    public function testParseStringClassWithProperties()
    {
        $ast = $this->parser->parseString('<?php class Test { private $foo; public $bar; protected $baz; public function getFoo() { return $this->foo; } public function setFoo($foo) { $this->foo = $foo;} } ?>');
        var_dump($ast);
    }
}

