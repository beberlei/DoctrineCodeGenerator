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

namespace Doctrine\CodeGenerator\Builder;

use PHPParser_Parser;
use PHPParser_Lexer;

/**
 * CodeBuilder that simplifies generation of common PHP statements.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class CodeBuilder
{
    private $parser;

    public function __construct(PHPParser_Parser $parser = null)
    {
        $this->parser = $parser ?: new PHPParser_Parser(new PHPParser_Lexer);
    }

    /**
     * Return a class-builder for a new class with the given name.
     *
     * @param string $className
     * @return ClassBuilder
     */
    public function classBuilder($className)
    {
        return new ClassBuilder($className);
    }

    /**
     * @return \Doctrine\CodeGenerator\Builder\MethodBuilder
     */
    public function method($name)
    {
        return new MethodBuilder($name);
    }

    public function property($name)
    {
        return new \PHPParser_Builder_Property($name);
    }

    /**
     * @param string $name
     * @return PHPParser_Node_Expr_PropertyFetch
     */
    public function instanceVariable($name)
    {
        return new \PHPParser_Node_Expr_PropertyFetch(
            $this->variable('this'), $name
        );
    }

    /**
     * @return PHPParser_Node_Expr_Variable
     */
    public function variable($name)
    {
        return new \PHPParser_Node_Expr_Variable($name);
    }

    /**
     * @return PHPParser_Node_Stmt_Return
     */
    public function returnStmt($expr)
    {
        return new \PHPParser_Node_Stmt_Return($expr);
    }

    /**
     * @return PHPParser_Node_Expr_Assign
     */
    public function assignment($left, $right)
    {
        return new \PHPParser_Node_Expr_Assign($left, $right);
    }

    /**
     * Return array of statements/expressions.
     *
     * @param string $string
     * @return array
     */
    public function code($string)
    {
        return $this->parser->parse("<?php\n " . $string . "?>");
    }

    /**
     * Return array of statements/expressions in class context.
     *
     * If you want to generate expresions for properties/methods of a class.
     *
     * @param string $string
     * @return array
     */
    public function classCode($string)
    {
        $stmts = $this->parser->parse("<?php\n class Dummy { " . $string . " }?>");
        return $stmts[0]->stmts;
    }

    /**
     * Generate an instantiate class expression from a given className.
     *
     * @param string $className
     * @return PHPParser_Node_Expr_New
     */
    public function instantiate($className)
    {
        return new \PHPParser_Node_Expr_New(new \PHPParser_Node_Name_FullyQualified($className));
    }
}

