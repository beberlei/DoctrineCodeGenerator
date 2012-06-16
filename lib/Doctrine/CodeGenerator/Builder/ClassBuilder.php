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

use PHPParser_Node_Stmt_Class;
use PHPParser_Node_Stmt_ClassMethod;
use PHPParser_Builder_Class;

/**
 * Class Builder
 */
class ClassBuilder extends PHPParser_Builder_Class
{
    /**
     * @return \Doctrine\CodeGenerator\Builder\MethodBuilder
     */
    public function method($name)
    {
        return new MethodBuilder($name);
    }

    public function findMethod($name)
    {
        foreach ($this->methods as $stmt) {
            if ( ($stmt instanceof \PHPParser_Node_Stmt_ClassMethod ||
                  $stmt instanceof \PHPParser_Node_Stmt_Function) &&
                 strtolower($stmt->name) === strtolower($name)) {

                return new MethodBuilder($stmt, $this);
            }
        }
        return $this->method($name);
    }

    /**
     * Does the class have the given method?
     *
     * @param string $name
     * @return bool
     */
    public function hasMethod($name)
    {
        return $this->getMethod($name) !== null;
    }

    /**
     * @param string $name
     * @return PHPParser_Node_Stmt_ClassMethod
     */
    public function getMethod($name)
    {
        foreach ($this->methods as $stmt) {
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
    public function property($name, $modifiers = PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC)
    {
        $pp = new \PHPParser_Node_Stmt_PropertyProperty($name);
        $property = new \PHPParser_Node_Stmt_Property($modifiers, array($pp));
        $this->properties[] = $property;
        return $this;
    }

    /**
     * Does the class has the property?
     *
     * @param string $name
     * @return bool
     */
    public function hasProperty($name)
    {
        return $this->getProperty($name) !== null;
    }

    /**
     * @param string $name
     * @return PHPParser_Node_Stmt_PropertyProperty
     */
    public function getProperty($name)
    {
        foreach ($this->properties as $stmt) {
            if ( ! ($stmt instanceof \PHPParser_Node_Stmt_Property)) {
                continue;
            }

            foreach ($stmt->props as $property) {
                if ($property->name === $name) {
                    return $property;
                }
            }
        }
        return null;
    }
}

