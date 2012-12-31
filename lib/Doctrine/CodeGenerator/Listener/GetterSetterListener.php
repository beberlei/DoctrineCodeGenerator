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

namespace Doctrine\CodeGenerator\Listener;

use Doctrine\CodeGenerator\GeneratorEvent;
use Doctrine\CodeGenerator\Builder\ClassBuilder;
use Doctrine\CodeGenerator\Builder\CodeBuilder;

/**
 * Each property is turned to protected and getters/setters are added.
 */
class GetterSetterListener extends AbstractCodeListener
{
    public function onGenerateProperty(GeneratorEvent $event)
    {
        $property = $event->getNode();
        //$property->makeProtected();

        $class       = $property->getClass();
        $code        = new CodeBuilder();

        $setName  = 'set' . ucfirst($property->getName());
        $getName  = 'get' . ucfirst($property->getName());

        if ($class->hasMethod($setName) || $class->hasMethod($getName)) {

            return;
        }

        $setter = $class->getMethod($setName);
        $setter->param($property->getName());
        $setter->append(array(
            $code->assignment(
                $code->instanceVariable($property->getName()), $code->variable($property->getName())
            )
        ));
        $setter->setAttribute('property', $property);

        $getter = $class->getMethod($getName);
        $getter->append(array($code->returnStmt($code->instanceVariable($property->getName()))));
        $getter->setAttribute('property', $property);
    }
}

