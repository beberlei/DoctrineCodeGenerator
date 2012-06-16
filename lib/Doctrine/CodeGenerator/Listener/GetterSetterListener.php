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

namespace Doctrine\CodeGenerator\Listener;

use Doctrine\CodeGenerator\GeneratorEvent;
use Doctrine\CodeGenerator\VoteGetterSetterEvent;
use Doctrine\CodeGenerator\Builder\ClassBuilder;
use Doctrine\CodeGenerator\Builder\Manipulator;

/**
 * Each property is turned to protected and getters/setters are added.
 */
class GetterSetterListener extends AbstractCodeListener
{
    public function onGenerateProperty(GeneratorEvent $event)
    {
        $propertyNode       = $event->getNode();
        $propertyNode->type = \PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED; // TODO: Move to Emitter and make configurable

        $class       = $propertyNode->getAttribute('parent');
        $code        = $this->code;
        $manipulator = new Manipulator;

        $property = $propertyNode->props[0];
        $setName  = 'set' . ucfirst($property->name);
        $getName  = 'get' . ucfirst($property->name);

        if ($manipulator->hasMethod($class, $setName) ||
            $manipulator->hasMethod($class, $getName)) {

            return;
        }

        $vote = new VoteGetterSetterEvent($propertyNode);
        $this->eventManager->dispatchEvent('onVoteGetterSetter', $vote);

        if ($vote->getAllowSetter()) {
            $setter = $manipulator->findMethod($class, $setName);
            $manipulator->param($setter, $property->name);
            $manipulator->append($setter, $code->assignment($code->instanceVariable($property->name), $code->variable($property->name)));
            $setter->setAttribute('property', $property);
        }

        if ($vote->getAllowGetter()) {
            $getter = $manipulator->findMethod($class, $getName);
            $manipulator->append($getter, $code->returnStmt($code->instanceVariable($property->name)));
            $getter->setAttribute('property', $property);
        }
    }

    public function getSubscribedEvents()
    {
        return array('onGenerateProperty');
    }
}

