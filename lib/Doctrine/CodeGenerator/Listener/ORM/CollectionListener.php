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

namespace Doctrine\CodeGenerator\Listener\ORM;

use Doctrine\CodeGenerator\Listener\AbstractCodeListener;
use Doctrine\CodeGenerator\Builder\CodeBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class CollectionListener extends AbstractCodeListener
{
    private $metadataFactory;

    public function injectMetadataFactory($metadata)
    {
        $this->metadataFactory = $metadata;
    }

    private function isToManyAssocation($propertyNode)
    {
        $mapping = $propertyNode->getAttribute('mapping');
        return $propertyNode->getAttribute('isAssociation') &&
               ($mapping['type'] & ClassMetadataInfo::TO_MANY) > 0;
    }

    public function onGenerateProperty($event)
    {
        $property = $event->getNode();

        if ( ! $this->isToManyAssocation($property)) {
            return;
        }

        $this->addConstructorInitialization($property);
        $this->addAdderMethod($property);
        $this->addRemoverMethod($property);
    }

    private function addConstructorInitialization($property)
    {
        $class       = $property->getClass();
        $constructor = $class->getMethod('__construct');
        $code        = new CodeBuilder();

        $constructor->append(array(
            $code->assignment(
                $code->instanceVariable($property->getName()),
                $code->instantiate('Doctrine\Common\Collections\ArrayCollection')
            )
        ));
    }

    private function addAdderMethod($property)
    {
        $class     = $property->getClass();
        $addMethod = 'add' . ucfirst($property->getName());

        if ($class->hasMethod($addMethod)) {
            return;
        }

        $mapping = $property->getAttribute('mapping');
        $code    = new CodeBuilder();
        $adder   = $class->getMethod($addMethod);
        $adder->param($property->getName(), null, $mapping['targetEntity']);
        $adder->append(array(
            $code->assignment(
                $code->arrayDimFetch($code->instanceVariable($property->getName())),
                $code->variable($property->getName())
            )
        ));
        $adder->setAttribute('property', $property);
    }

    private function addRemoverMethod($property)
    {
        $class        = $property->getClass();
        $removeMethod = 'remove' . ucfirst($property->getName());

        if ($class->hasMethod($removeMethod)) {
            return;
        }

        $mapping = $property->getAttribute('mapping');
        $code    = new CodeBuilder();
        $remover = $class->getMethod($removeMethod);
        $remover->param($property->getName(), null, $mapping['targetEntity']);
        $remover->append(array(
            $code->methodCall(
                $code->instanceVariable($property->getName()),
                'removeElement',
                array($property->getName())
            )
        ));
    }
}

