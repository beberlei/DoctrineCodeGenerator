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

namespace Doctrine\CodeGenerator\Listener\ORM;

use Doctrine\CodeGenerator\GenerationProject;
use Doctrine\CodeGenerator\Builder\ClassBuilder;
use Doctrine\CodeGenerator\Listener\AbstractCodeListener;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;
use Doctrine\CodeGenerator\ProjectEvent;

/**
 * Generate ORM Classes from Database or Schema data
 */
class GenerateProjectListener extends AbstractCodeListener
{
    private $metadataFactory;

    public function injectMetadataFactory($metadata)
    {
        $this->metadataFactory = $metadata;
    }

    public function onStartGeneration(ProjectEvent $event)
    {
        $project = $event->getProject();

        foreach ($this->metadataFactory->getAllMetadata() as $metadata) {
            $classNode = $this->generateClass($metadata);

            $file = $project->getEmptyClass($metadata->name);
            $file->append($classNode);
        }
    }

    /**
     * Generate a Class Node for the given Metadata
     *
     * @param ClassMetadataInfo $metadata
     * @return PHPParser_Node_Stmt_Class
     */
    public function generateClass(ClassMetadataInfo $metadata)
    {
        $builder = $this->code->classBuilder($metadata->name);

        foreach ($metadata->fieldMappings as $fieldName => $fieldMapping) {
            $builder->property($fieldName);
            $property = $builder->getProperty($fieldName);

            $property->setAttribute('isColumn', true);
            $property->setAttribute('type', $fieldMapping['type']);
            $property->setAttribute('mapping', $fieldMapping);
        }

        foreach ($metadata->associationMappings as $assocName => $assoc) {
            $builder->property($assocName);
            $property = $builder->getProperty($assocName);

            $property->setAttribute('isAssociation', true);
            $property->setAttribute('type', $assoc['targetEntity']);
            $property->setAttribute('mapping', $assoc);
        }

        return $builder->getNode();
    }
}

