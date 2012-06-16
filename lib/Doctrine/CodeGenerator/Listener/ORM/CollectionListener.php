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
use Doctrine\CodeGenerator\Builder\Manipulator;

class CollectionListener extends AbstractCodeListener
{
    private $metadataFactory;

    public function injectMetadataFactory($metadata)
    {
        $this->metadataFactory = $metadata;
    }

    public function onVoteGetterSetter($event)
    {
        $propertyNode = $event->getNode();

        if ($this->isToManyAssocation($propertyNode)) {
            $event->denySetter();
        }
    }

    private function isToManyAssocation($propertyNode)
    {
        $mapping = $propertyNode->props[0]->getAttribute('mapping');
        return $propertyNode->props[0]->getAttribute('isAssociation') &&
               ($mapping['type'] & ClassMetadataInfo::TO_MANY) > 0;
    }

    public function onGenerateProperty($event)
    {
        $propertyNode = $event->getNode();

        if ( ! $this->isToManyAssocation($propertyNode)) {
            return;
        }

        $class       = $propertyNode->getAttribute('parent');
        $code        = $this->code;
        $manipulator = new Manipulator;
        $property    = $propertyNode->props[0];
        $addMethod   = 'add' . ucfirst($property->name);

        if ($manipulator->hasMethod($class, $addMethod)) {
            return;
        }

        $adder = $manipulator->findMethod($class, $addMethod);
        $manipulator->param($adder, $property->name);
        $manipulator->append($adder, $code->assignment(new \PHPParser_Node_Expr_ArrayDimFetch($code->instanceVariable($property->name)), $code->variable($property->name)));
        $adder->setAttribute('property', $property);
    }
}

