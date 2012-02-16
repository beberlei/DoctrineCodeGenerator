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

use PHPParser_Node_Stmt_Class;
use PHPParser_Node_Stmt_ClassMethod;

class ClassBuilder
{
    private $class;

    static public function newClass($name)
    {
        return new self(new PHPParser_Node_Stmt_Class($name));
    }

    public function __construct(PHPParser_Node_Stmt_Class $class)
    {
        $this->class = $class;
    }

    public function getNode()
    {
        return $this->class;
    }

    /**
     * @return \Doctrine\CodeGenerator\Builder\MethodBuilder
     */
    public function appendMethod($name)
    {
        $method = new PHPParser_Node_Stmt_ClassMethod($name);
        $methodBuilder = new MethodBuilder($method, $this);
        $this->class->stmts[] = $method;
        return $methodBuilder;
    }

    public function findMethod($name)
    {
        foreach ($this->class->stmts as $stmt) {
            if ( ($stmt instanceof \PHPParser_Node_Stmt_ClassMethod ||
                  $stmt instanceof \PHPParser_Node_Stmt_Function) &&
                strtolower($stmt->name) === strtolower($name)) {
                return $stmt;
            }
        }
        return $this->appendMethod($name);
    }

    public function hasMethod($name)
    {
        foreach ($this->class->stmts as $stmt) {
            if ( ($stmt instanceof \PHPParser_Node_Stmt_ClassMethod ||
                  $stmt instanceof \PHPParser_Node_Stmt_Function) &&
                strtolower($stmt->name) === strtolower($name)) {
                return true;
            }
        }
        return false;
    }

    public function getMethod($name)
    {
        foreach ($this->class->stmts as $stmt) {
            if ( ($stmt instanceof \PHPParser_Node_Stmt_ClassMethod ||
                  $stmt instanceof \PHPParser_Node_Stmt_Function) &&
                strtolower($stmt->name) === strtolower($name)) {
                return $stmt;
            }
        }
        return null;
    }

    /**
     * Add a new property with given name.
     *
     * No support for multiple names per modifier and no checks for already
     * existing properties.
     *
     * @param string $name
     * @param int $modifiers
     * @return \Doctrine\CodeGenerator\Builder\ClassBuilder
     */
    public function appendProperty($name, $modifiers = PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC)
    {
        $pp = new \PHPParser_Node_Stmt_PropertyProperty($name);
        $property = new \PHPParser_Node_Stmt_Property($modifiers, array($pp));
        $this->class->stmts[] = $property;
        return $this;
    }

    /**
     * @param string $name
     * @return PHPParser_Node_Stmt_PropertyProperty
     */
    public function getProperty($name)
    {
        foreach ($this->class->stmts as $stmt) {
            if ($stmt instanceof \PHPParser_Node_Stmt_Property) {
                foreach ($stmt->props as $property) {
                    if ($property->name === $name) {
                        return $property;
                    }
                }
            }
        }
        return null;
    }

    /**
     * @return ClassBuilder
     */
    public function append($stmt)
    {
        foreach ((array)$stmt as $s) {
            $this->class->stmts[] = $s;
        }
        return $this;
    }
}

