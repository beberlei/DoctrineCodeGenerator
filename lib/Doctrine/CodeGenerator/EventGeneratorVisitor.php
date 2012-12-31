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

namespace Doctrine\CodeGenerator;

use Doctrine\CodeGenerator\Builder\Visitor;
use Doctrine\CodeGenerator\Builder\ClassBuilder;
use Doctrine\CodeGenerator\Builder\MethodBuilder;
use Doctrine\CodeGenerator\Builder\PropertyBuilder;
use Doctrine\CodeGenerator\Builder\FunctionBuilder;
use Doctrine\Common\EventManager;

use Countable;
use SplObjectStorage;

class EventGeneratorVisitor implements Visitor, Countable
{
    private $visited;
    private $project;
    private $evm;

    public function __construct(EventManager $evm, GenerationProject $project)
    {
        $this->evm     = $evm;
        $this->project = $project;
        $this->visited = new SplObjectStorage();
    }

    public function visitClass(ClassBuilder $class)
    {
        $this->dispatch($class, "onGenerateClass");
    }

    public function visitMethod(MethodBuilder $method)
    {
        $methodName = $method->getName();

        if (substr($methodName, 0, 3) === "set") {
            $eventName = "onGenerateSetter";
        } else if (substr($methodName, 0, 3) === "get") {
            $eventName = "onGenerateGetter";
        } else if ($methodName === "__construct") {
            $eventName = "onGenerateConstructor";
        } else {
            $eventName = "onGenerateMethod";
        }

        $this->dispatch($method, $eventName);
    }

    public function visitProperty(PropertyBuilder $property)
    {
        $this->dispatch($property, "onGenerateProperty");
    }

    private function dispatch($builder, $eventName)
    {
        if ($this->visited->contains($builder)) {
            return;
        }

        $this->visited->attach($builder);

        $this->evm->dispatchEvent($eventName, new GeneratorEvent($builder, $this->project));
    }

    public function count()
    {
        return count($this->visited);
    }
}

