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
class Manipulator extends PHPParser_BuilderAbstract
{
    /**
     * Adds a property to a class. If the property already exists, its
     * overwritten.
     *
     * @param PHPParser_Node_Stmt_Class $class
     * @param PHPParser_Builder_Property|PHPParser_Node_Stmt_Property
     * @return Manipulator
     */
    public function addProperty(PHPParser_Node_Stmt_Class $class, $property)
    {
        $newProperty     = $this->normalizeNode($property);
        $lastPropertyNum = 0;

        foreach ($class->stmts as $num => $stmt) {
            if ($stmt instanceof PHPParser_Node_Stmt_Property) {
                if ($stmt->props[0]->name === $newProperty->props[0]->name) {
                    $class->stmts[$num] = $newProperty;

                    return $this;
                }

                $lastPropertyNum = $num;
            }
        }

        $before       = array_slice($class->stmts, 0, $lastPropertyNum);
        $after        = array_slice($class->stmts, $lastPropertyNum);
        $class->stmts = array_merge($before, array($newProperty), $after);

        return $this;
    }

    /**
     * Append Statement(s) to bottom of child statements of given stmt.
     *
     * @param PHPParser_Node_Stmt $parent
     * @param array|PHPParser_Node_Stmt $newStatements
     * @return Manipulator
     */
    public function append(PHPParser_Node_Stmt $parent, $newStatements)
    {
        if ( ! is_array($newStatements)) {
            $newStatements = array($newStatements);
        }

        if ( ! is_array($parent->stmts)) {
            throw new \RuntimeException("Statment Node " . get_class($parent) . " has no subnodes 'stmts'");
        }

        $parent->stmts = array_merge($parent->stmts, array_values($newStatements));
        return $this;
    }

    /**
     * Find existing method or create new method node if none is found on
     * class.
     *
     * @param PHPParser_Node_Stmt_Class $class
     * @param string $methodName
     * @return PHPParser_Node_Stmt_ClassMethod
     */
    public function findMethod(PHPParser_Node_Stmt_Class $class, $methodName)
    {
        foreach ($class->stmts as $stmt) {
            if ( ($stmt instanceof \PHPParser_Node_Stmt_ClassMethod ||
                  $stmt instanceof \PHPParser_Node_Stmt_Function) &&
                 strtolower($stmt->name) === strtolower($methodName)) {

                return $stmt;
            }
        }

        $stmt           = new PHPParser_Node_Stmt_ClassMethod($methodName);
        $class->stmts[] = $stmt;

        return $stmt;
    }

    public function getProperty(PHPParser_Node_Stmt_Class $class, $propertyName)
    {
        foreach ($class->stmts as $stmt) {
            if ( $stmt instanceof \PHPParser_Node_Stmt_Property &&
                 $stmt->props[0]->name === $propertyName) {

                return $stmt->props[0];
            }
        }

        return null;
    }

    public function findProperty(PHPParser_Node_Stmt_Class $class, $propertyName)
    {
        $stmt = $this->getProperty($class, $propertyName);

        if ($stmt) {
            return $stmt;
        }

        // not efficient, but it works as intended
        $this->addProperty($class, $propertyName);
        return $this->findProperty($class, $propertyName);
    }

    /**
     * Check if class has a method with given name.
     *
     * @return bool
     */
    public function hasMethod(PHPParser_Node_Stmt_Class $class, $methodName)
    {
        foreach ($class->stmts as $stmt) {
            if ( ($stmt instanceof \PHPParser_Node_Stmt_ClassMethod ||
                  $stmt instanceof \PHPParser_Node_Stmt_Function) &&
                 strtolower($stmt->name) === strtolower($methodName)) {

                return true;
            }
        }

        return false;
    }

    /**
     * Check if class has a property with given name.
     *
     * @return bool
     */
    public function hasProperty(PHPParser_Node_Stmt_Class $class, $propertyName)
    {
        foreach ($class->stmts as $stmt) {
            if ( ($stmt instanceof \PHPParser_Node_Stmt_Property) &&
                 $stmt->props[0]->name == $propertyName) {
                return true;
            }
        }

        return false;
    }

    public function getNode()
    {
        throw new \BadMethodCallException("Not valid on this builder!");
    }

    public function param(PHPParser_Node_Stmt_ClassMethod $method, $name, $default = null, $type = null, $byRef = false)
    {
        $param = new PHPParser_Node_Param($name, $default, $type, $byRef);
        $method->params[] = $param;
        return $this;
    }

    /**
     * Set or overwrite the doc comment of a given node.
     *
     * @param PHPParser_Node $node
     * @param string $comment
     * @return void
     */
    public function setDocComment(\PHPParser_Node $node, $comment)
    {
        $doc      = $node->getDocComment();
        $comments = $node->getAttribute('comments');
        if ($doc) {
            unset($comments[ count($comments) - 1]);
        }

        $comments[] = new \PHPParser_Comment_Doc($comment);
        $node->setAttribute('comments', $comments);
    }
}

