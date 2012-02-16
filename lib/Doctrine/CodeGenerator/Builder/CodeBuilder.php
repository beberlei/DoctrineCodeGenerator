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
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\CodeGenerator\Builder;

class CodeBuilder
{
    private $parser;

    public function __construct(\PHPParser_Parser $parser = null)
    {
        $this->parser = $parser ?: new \PHPParser_Parser();
    }

    /**
     * @param string $name
     * @return PHPParser_Node_Expr_PropertyFetch
     */
    public function instanceVariable($name)
    {
        return new \PHPParser_Node_Expr_PropertyFetch(
            new \PHPParser_Node_Expr_Variable('this'), $name
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

    public function code($string)
    {
        return $this->parser->parse(new \PHPParser_Lexer("<?php\n " . $string . "?>"));
    }

    public function classCode($string)
    {
        $stmts = $this->parser->parse(new \PHPParser_Lexer("<?php\n class Dummy { " . $string . " }?>"));
        return $stmts[0]->stmts;
    }

    public function instantiate($className)
    {
        return new \PHPParser_Node_Expr_New(new \PHPParser_Node_Name_FullyQualified($className));
    }
}

