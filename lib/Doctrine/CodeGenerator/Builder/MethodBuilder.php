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

use PHPParser_Node_Stmt_ClassMethod;
use PHPParser_Node_Param;
use PHPParser_Builder_Method;

class MethodBuilder extends AbstractBuilder
{
    private $builder;
    private $class;
    private $params;

    public function __construct($name, ClassBuilder $class)
    {
        parent::__construct($name);

        $this->class   = $class;
        $this->builder = new PHPParser_Builder_Method($name);
    }

    public function getClass()
    {
        return $this->class;
    }

    public function append($stmts)
    {
        $this->builder->addStmts($stmts);
        return $this;
    }

    /**
     * @return MethodBuilder
     */
    public function param($name, $default = null, $type = null, $byRef = false)
    {
        $param = new PHPParser_Node_Param($name, $default, $type, $byRef);
        $this->params[] = $param;
        return $this;
    }

    public function visit(Visitor $visitor)
    {
        $visitor->visitMethod($this);
    }

    /**
     * @return PHPParser_Node_Param
     */
    public function getNode()
    {
        $node = $this->builder->getNode();
        //$node->params = $this->params;
        return $node;
    }
}

