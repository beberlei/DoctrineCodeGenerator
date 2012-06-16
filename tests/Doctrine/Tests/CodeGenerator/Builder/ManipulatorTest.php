<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\Tests\CodeGenerator\Builder;

use PHPParser_Node_Stmt_Class;
use PHPParser_Node_Stmt_Property;
use PHPParser_Builder_Property;
use Doctrine\CodeGenerator\Builder\Manipulator;
use Doctrine\CodeGenerator\Builder\CodeBuilder;

class ManipulatorTest extends \PHPUnit_Framework_TestCase
{
    public function testAddPropertyFromBuilder()
    {
        $class       = new PHPParser_Node_Stmt_Class("Test");
        $manipulator = new Manipulator();
        $builder     = new PHPParser_Builder_Property("foo");

        $manipulator->addProperty($class, $builder);

        $this->assertEquals($builder->getNode(), $class->stmts[0]);
    }

    public function testAddPropertyFromProperty()
    {
        $class       = new PHPParser_Node_Stmt_Class("Test");
        $manipulator = new Manipulator();
        $builder     = new PHPParser_Builder_Property("foo");

        $manipulator->addProperty($class, $builder->getNode());

        $this->assertEquals($builder->getNode(), $class->stmts[0]);
    }

    public function testAddExistingPropertyReplaces()
    {
        $class         = new PHPParser_Node_Stmt_Class("Test");
        $manipulator   = new Manipulator();
        $builder       = new PHPParser_Builder_Property("foo");
        $builderStatic = new PHPParser_Builder_Property("foo");

        $builderStatic->makeStatic();
        $manipulator->addProperty($class, $builder);
        $manipulator->addProperty($class, $builderStatic);

        $this->assertEquals(1, count($class->stmts));
        $this->assertNotEquals($builder->getNode(), $class->stmts[0]);
        $this->assertEquals($builderStatic->getNode(), $class->stmts[0]);
    }

    public function testAddPropertyBehindExisting()
    {
        $class       = new PHPParser_Node_Stmt_Class("Test");
        $manipulator = new Manipulator();
        $builderFoo  = new PHPParser_Builder_Property("foo");
        $builderBar  = new PHPParser_Builder_Property("bar");

        $manipulator->addProperty($class, $builderFoo);
        $manipulator->addProperty($class, $builderBar);
        $manipulator->addProperty($class, $builderFoo);
        $manipulator->addProperty($class, $builderBar);

        $this->assertEquals(2, count($class->stmts));
    }

    public function testAppendToClass()
    {
        $class       = new PHPParser_Node_Stmt_Class("Test");
        $manipulator = new Manipulator();
        $codeBuilder = new CodeBuilder();

        $manipulator->append($class, $codeBuilder->method('foo'));

        $this->assertEquals(1, count($class->stmts));
    }

    public function testAppendManyStatementsToClass()
    {
        $class       = new PHPParser_Node_Stmt_Class("Test");
        $manipulator = new Manipulator();
        $codeBuilder = new CodeBuilder();

        $manipulator->append($class, array(
            $codeBuilder->method('foo'),
            $codeBuilder->method('bar')
        ));

        $this->assertEquals(2, count($class->stmts));
    }

    public function testAppendStatementToInvalidStmt()
    {
        $echo = new \PHPParser_Node_Stmt_Echo(array());

        $manipulator = new Manipulator();
        $codeBuilder = new CodeBuilder();

        $this->setExpectedException("RuntimeException", "Statment Node PHPParser_Node_Stmt_Echo has no subnodes 'stmts'");
        $manipulator->append($echo, $codeBuilder->method('foo'));
    }

    public function testFindMethod()
    {
        $class       = new PHPParser_Node_Stmt_Class("Test");
        $manipulator = new Manipulator();

        $method = $manipulator->findMethod($class, '__construct');
        $this->assertInstanceOf('PHPParser_Node_Stmt_ClassMethod', $method);
        $this->assertEquals(1, count($class->stmts));
    }

    public function testHasMethod()
    {
        $class       = new PHPParser_Node_Stmt_Class("Test");
        $manipulator = new Manipulator();

        $this->assertFalse($manipulator->hasMethod($class, '__construct'));
        $method = $manipulator->findMethod($class, '__construct');
        $this->assertTrue($manipulator->hasMethod($class, '__construct'));
    }

    public function testHasProperty()
    {
        $class       = new PHPParser_Node_Stmt_Class("Test");
        $manipulator = new Manipulator();
        $builder     = new PHPParser_Builder_Property("foo");

        $this->assertFalse($manipulator->hasProperty($class, 'foo'));
        $manipulator->addProperty($class, $builder);
        $this->assertTrue($manipulator->hasProperty($class, 'foo'));
    }
}

