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

use PHPParser_Builder_Class;

/**
 * Class Builder
 */
class ClassBuilder extends AbstractBuilder
{
    private $name;
    private $builder;
    private $properties = array();
    private $constants  = array();
    private $methods    = array();

    public function __construct($name)
    {
        parent::__construct($name);
        $this->builder = new PHPParser_Builder_Class($name);
    }

    public function extend($parentClassName)
    {
        $this->builder->extend($parentClassName);
        return $this;
    }

    public function implement($interfaceName)
    {
        $this->builder->implement($interfaceName);
        return $this;
    }

    public function makeAbstract()
    {
        $this->builder->makeAbstract();
        return $this;
    }

    public function makeFinal()
    {
        $this->builder->makeFinal();
        return $this;
    }

    /**
     * Does the class have the given method?
     *
     * @param string $name
     * @return bool
     */
    public function hasMethod($name)
    {
        return isset($this->methods[$name]);
    }

    /**
     * @param string $name
     * @return MethodBuilder
     */
    public function getMethod($name)
    {
        if ( ! isset($this->methods[$name])) {
            $this->methods[$name] = new MethodBuilder($name, $this);
        }

        return $this->methods[$name];
    }

    /**
     * Does the class has the property?
     *
     * @param string $name
     * @return bool
     */
    public function hasProperty($name)
    {
        return isset($this->properties[$name]);
    }

    /**
     * @param string $name
     * @return PropertyBuilder
     */
    public function getProperty($name)
    {
        if ( ! $this->hasProperty($name)) {
            $this->properties[$name] = new PropertyBuilder($name, $this);
        }

        return $this->properties[$name];
    }

    public function append($stmts)
    {
        $this->builder->addStmts($stmts);
        return $this;
    }

    public function getNode()
    {
        $manipulator = new Manipulator();
        foreach ($this->properties as $property) {
            $property = $manipulator->setDocComment($property->getNode(), $property->getDocComment());
            $this->builder->addStmt($property);
        }

        foreach ($this->methods as $method) {
            $method = $manipulator->setDocComment($method->getNode(), $method->getDocComment());
            $this->builder->addStmt($method);
        }

        return $manipulator->setDocComment($this->builder->getNode(), $this->getDocComment());
    }

    public function visit(Visitor $visitor)
    {
        $visitor->visitClass($this);

        foreach ($this->properties as $property) {
            $visitor->visitProperty($property);
        }

        foreach ($this->methods as $method) {
            $visitor->visitMethod($method);
        }
    }
}

