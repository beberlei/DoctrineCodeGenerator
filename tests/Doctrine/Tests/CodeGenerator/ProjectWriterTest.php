<?php

namespace Doctrine\Tests\CodeGenerator;

use Doctrine\CodeGenerator\GenerationProject;
use Doctrine\CodeGenerator\ProjectWriter;

class ProjectWriterTest extends \PHPUnit_Framework_TestCase
{
    private $dir;

    public function setUp()
    {
        $this->dir = sys_get_temp_dir() . '/dcg';
    }

    public function testWrite()
    {
        $project = new GenerationProject();
        $project->getClass('stdClass');

        $writer = new ProjectWriter($this->dir);
        $writer->write($project);

        $this->assertTrue(file_exists($this->dir . '/stdClass.php'));
    }
}

