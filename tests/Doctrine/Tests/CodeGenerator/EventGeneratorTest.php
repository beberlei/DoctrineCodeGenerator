<?php

namespace Doctrine\Tests\CodeGenerator;

use Doctrine\CodeGenerator\GenerationProject;
use Doctrine\CodeGenerator\ProjectEvent;
use Doctrine\CodeGenerator\EventGenerator;

class EventGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateEmitsOnStartGeneration()
    {
        $project = new GenerationProject();
        $evm     = $this->getMock('Doctrine\Common\EventManager');

        $evm->expects($this->once())->method('dispatchEvent')
            ->with('onStartGeneration', new ProjectEvent($project));

        $generator = new EventGenerator($evm);
        $generator->generate($project);
    }
}

