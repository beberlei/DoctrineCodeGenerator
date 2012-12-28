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

        $class       = $property->getClass();
        $code        = $this->code;
        $addMethod   = 'add' . ucfirst($property->getName());

        if ($class->hasMethod($addMethod)) {
            return;
        }

        $adder = $class->getMethod($addMethod);
        $adder->param($property->getName());
        $adder->append(array(
            $code->assignment(
                $code->arrayDimFetch($code->instanceVariable($property->name)),
                $code->variable($property->getName())
            )
        ));
        $adder->setAttribute('property', $property);
    }
}

