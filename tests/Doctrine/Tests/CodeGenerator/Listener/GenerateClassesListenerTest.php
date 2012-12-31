<?php

namespace Doctrine\Tests\CodeGenerator\Listener;

use Doctrine\CodeGenerator\GenerationProject;
use Doctrine\CodeGenerator\ProjectEvent;
use Doctrine\CodeGenerator\Listener\GenerateClassesListener;

class GenerateClassesListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testOnStartGeneration()
    {
        $project = new GenerationProject();
        $listener = new GenerateClassesListener(array(
            'classes' => array(
                'Test' => array(
                    'properties' => array('foo' => array(), 'bar' => array())
                )
            )
        ));
        $listener->onStartGeneration(new ProjectEvent($project));

        $builder = $project->getClass('Test');

        $this->assertTrue($builder->hasProperty('foo'));
        $this->assertTrue($builder->hasProperty('bar'));
    }
}
