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

    public function testGetClass()
    {
        $project = new GenerationProject();

        $this->assertInstanceOf('Doctrine\CodeGenerator\Builder\ClassBuilder', $project->getClass('stdClass'));
    }

    public function testGetSameClass()
    {
        $project = new GenerationProject();
        $classA = $project->getClass('stdClass');
        $classB = $project->getClass('stdClass');

        $this->assertSame($classA, $classB);
    }

    public function testGetClasses()
    {
        $project = new GenerationProject();
        $classA = $project->getClass('classA');
        $classB = $project->getClass('classB');

        $this->assertEquals(2, count($project->getClasses()));
    }

    public function testGetFiles()
    {
        $project = new GenerationProject();
        $classA = $project->getClass('stdClass');

        $files = $project->getFiles();
        $this->assertEquals(1, count($files));
        $this->assertContainsOnly('Doctrine\CodeGenerator\File', $files);
        $this->assertEquals('stdClass.php', $files[0]->getPath());
    }
}

