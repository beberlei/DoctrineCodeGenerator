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
use Doctrine\Common\EventManager;
use Doctrine\CodeGenerator\GeneratorEvent;
use SplObjectStorage;

/**
 * Powerhouse of the CodeGenerator, it will listen to all the fun stuff and
 * trigger events for interesing PHP statements.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class EventsVisitor extends PHPParser_NodeVisitorAbstract
{
    private $visited;
    private $evm;
    private static $visitedClasses = array(
        'PHPParser_Node_Stmt_Class' => 'onGenerateClass',
        'PHPParser_Node_Stmt_ClassMethod' => 'onGenerateMethod',
        'PHPParser_Node_Stmt_Function' => 'onGenerateFunction',
        'PHPParser_Node_Stmt_Property' => 'onGenerateProperty',
        'PHPParser_Node_Stmt_Trait' => 'onGenerateTrait',
        'PHPParser_Node_Stmt_Interface' => 'onGenerateInterface',
        'PHPParser_Node_Param' => 'onGenerateParameter',
    );
    private $parentStorage;
    private $counter;

    public function __construct(EventManager $evm, $parentStorage = null)
    {
        $this->visited = new SplObjectStorage();
        $this->evm = $evm;
        $this->parentStorage = $parentStorage;
    }

    public function beforeTraverse(array $nodes)
    {
        $this->counter = 0;
    }

    public function getCounter()
    {
        return $this->counter;
    }

    public function enterNode(PHPParser_Node $node)
    {
        if ($this->visited->contains($node)) {
            return;
        }

        $event = $this->getEvent($node);
        if (!$event) {
            return;
        }

        $this->evm->dispatchEvent($event, new GeneratorEvent($node, $this->parentStorage));
        $this->visited->attach($node);
        $this->counter++;
    }

    public function leaveNode(PHPParser_Node $node)
    {
        $event = $this->getEvent($node);
        if (!$event) {
            return;
        }
        $event = "post" . substr($event, 2);

        $this->evm->dispatchEvent($event, new GeneratorEvent($node, $this->parentStorage));
    }

    private function getEvent($node)
    {
        $class = get_class($node);

        if ( ! isset(self::$visitedClasses[$class])) {
            return;
        }

        $event = self::$visitedClasses[$class];

        if ($node instanceof \PHPParser_Node_Stmt_ClassMethod) {
            if (substr($node->name, 0, 3) == "set") {
                $event = 'onGenerateSetter';
            } else if (substr($node->name, 0, 3) == "get") {
                $event = 'onGenerateGetter';
            } else if ($node->name == "__construct") {
                $event = 'onGenerateConstructor';
            }
        }
        return $event;
    }
}

