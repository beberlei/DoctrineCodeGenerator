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

namespace Doctrine\CodeGenerator;

use SplObjectStorage;
use PHPParser_Node;
use PHPParser_Node_Stmt_Class;

class MetadataContainer
{
    private $nodes;

    public function __construct()
    {
        $this->nodes = new SplObjectStorage;
    }

    public function addTag(PHPParser_Node $node, $tag)
    {
        $this->nodes->attach($node);
        $values = $this->nodes[$node];
        $values['tags'][] = $tag;
        $this->nodes[$node] = $values;
        return $this;
    }

    public function hasTag(PHPParser_Node $node, $name)
    {
        if (!$this->nodes->contains($node)) {
            return false;
        }
        $values = $this->nodes[$node];
        if (isset($values['tags']) && in_array($name, $values['tags'])) {
            return true;
        }
        return false;
    }

    public function setAttribute(PHPParser_Node $node, $name, $value)
    {
        $this->nodes->attach($node);
        $values = $this->nodes[$node];
        $values['attributes'][$name] = $value;
        $this->nodes[$node] = $values;
        return $this;
    }

    public function getAttribute(PHPParser_Node $node, $name)
    {
        if (!$this->nodes->contains($node)) {
            return null;
        }
        $values = $this->nodes[$node];
        if (isset($values['attributes'][$name])) {
            return $values['attributes'][$name];
        }
        return null;
    }

    public function setClassFor(PHPParser_Node $node, PHPParser_Node_Stmt_Class $class)
    {
        $this->nodes->attach($node);
        $values = $this->nodes[$node];
        $values['class']= $class;
        $this->nodes[$node] = $values;
    }

    public function getClassFor(PHPParser_Node $node)
    {
        if (!$this->nodes->contains($node)) {
            throw new \InvalidArgumentException("Not a managed node!");
        }
        $values = $this->nodes[$node];
        if ( ! isset($values['class'])) {
            throw new \InvalidArgumentException("This node has no class.");
        }
        return $values['class'];
    }

    public function setParent(PHPParser_Node $node, PHPParser_Node $parent)
    {
        $this->nodes->attach($node);
        $values = $this->nodes[$node];
        $values['parent']= $parent;
        $this->nodes[$node] = $values;
    }

    public function getParent(PHPParser_Node $node)
    {
        if (!$this->nodes->contains($node)) {
            throw new \InvalidArgumentException("Not a managed node!");
        }
        $values = $this->nodes[$node];
        if ( ! isset($values['parent'])) {
            throw new \InvalidArgumentException("This node has no parent.");
        }
        return $values['parent'];
    }
}

