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

use PHPParser_Builder_Property;

class PropertyBuilder extends AbstractBuilder
{
    private $builder;

    public function __construct($name)
    {
        parent::__construct($name);
        $this->builder = new PHPParser_Builder_Property($name);
    }

    public function makeStatic()
    {
        $this->builder->makeStatic();
        return $this;
    }

    public function makePublic()
    {
        $this->builder->makePublic();
        return $this;
    }

    public function makeProtected()
    {
        $this->builder->makeProtected();
        return $this;
    }

    public function makePrivate()
    {
        $this->builder->makePrivate();
        return $this;
    }

    public function setDefault($value)
    {
        $this->builder->setDefault($value);
        return $this;
    }

    public function getNode()
    {
        return $this->builder->getNode();
    }

    public function visit(Visitor $visitor)
    {
        $visitor->visitProperty($this);
    }
}

