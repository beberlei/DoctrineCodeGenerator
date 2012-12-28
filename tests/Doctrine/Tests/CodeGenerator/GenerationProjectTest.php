<?php
namespace Doctrine\Tests\CodeGenerator;

use Doctrine\CodeGenerator\GenerationProject;

class GenerationProjectTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $project = new GenerationProject();
        $this->assertEquals(0, count($project->getFiles()));
    }
}

