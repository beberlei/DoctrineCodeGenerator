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

namespace Doctrine\CodeGenerator\Visitor;

use PHPParser_NodeVisitorAbstract;
use PHPParser_Node;

/**
 * Powerhouse of the CodeGenerator, it will listen to all the fun stuff and
 * trigger events for interesing PHP statements.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class ParentVisitor extends PHPParser_NodeVisitorAbstract
{
    private $class;
    private $parents;

    public function enterNode(PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_Stmt_Class) {
            $this->class = $node;
        } else if ($node instanceof \PHPParser_Node_Stmt_ClassMethod) {
            $this->parents[spl_object_hash($node)] = $this->class;
        } else if ($node instanceof \PHPParser_Node_Stmt_PropertyProperty) {
            $this->parents[spl_object_hash($node)] = $this->class;
        } else if ($node instanceof \PHPParser_Node_Stmt_Property) {
            $this->parents[spl_object_hash($node)] = $this->class;
        }
    }

    public function getParent(PHPParser_Node $node)
    {
        if (isset ($this->parents[spl_object_hash($node)])) {
            return $this->parents[spl_object_hash($node)];
        }
        return null;
    }
}
