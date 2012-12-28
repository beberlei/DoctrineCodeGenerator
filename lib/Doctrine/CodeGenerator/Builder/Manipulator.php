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

use PHPParser_BuilderAbstract;
use PHPParser_Node_Stmt;
use PHPParser_Node_Param;
use PHPParser_Node_Stmt_Class;
use PHPParser_Node_Stmt_Property;
use PHPParser_Node_Stmt_ClassMethod;

/**
 * Manipulator has API to change existing structures through convenience
 * methods.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class Manipulator
{
    /**
     * Set or overwrite the doc comment of a given node.
     *
     * @param PHPParser_Node $node
     * @param string $comment
     * @return void
     */
    public function setDocComment(\PHPParser_Node $node, $comment)
    {
        if (!$comment) {
            return $node;
        }

        $doc      = $node->getDocComment();
        $comments = $node->getAttribute('comments');

        if ($doc) {
            unset($comments[ count($comments) - 1]);
        }

        $comments[] = new \PHPParser_Comment_Doc($comment);
        $node->setAttribute('comments', array_values($comments));

        return $node;
    }
}

