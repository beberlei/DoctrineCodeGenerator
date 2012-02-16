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

use PHPParser_Node_Stmt_ClassMethod;
use PHPParser_Node_Param;

class MethodBuilder
{
    private $node;
    private $classBuilder;

    public function __construct(PHPParser_Node_Stmt_ClassMethod $node, ClassBuilder $classBuilder = null)
    {
        $this->node = $node;
        $this->classBuilder = $classBuilder;
    }

    public function end()
    {
        return $this->classBuilder;
    }

    public function getNode()
    {
        return $this->node;
    }

    /**
     * @return MethodBuilder
     */
    public function addParam($name, $default = null, $type = null, $byRef = false)
    {
        $param = new PHPParser_Node_Param($name, $default, $type, $byRef);
        $this->node->params[] = $param;
        return $this;
    }

    /**
     * @return MethodBuilder
     */
    public function append($stmt)
    {
        if (!is_array($stmt)) {
            $stmt = array($stmt);
        }
        foreach ($stmt as $s) {
            if (!($s instanceof \PHPParser_Node)) {
                throw new \InvalidArgumentException("$s is not a node.");
            }
            $this->node->stmts[] = $s;
        }
        return $this;
    }
}


