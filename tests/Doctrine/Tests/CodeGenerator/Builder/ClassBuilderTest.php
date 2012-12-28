<?php

namespace Doctrine\Tests\CodeGenerator\Builder;

use Doctrine\CodeGenerator\Builder\ClassBuilder;
use Doctrine\Tests\CodeGenerator\TestCase;

class ClassBuilderTest extends TestCase
{
    public function testMethod()
    {
        $builder = new ClassBuilder("Test");
        $this->assertInstanceOf("Doctrine\CodeGenerator\Builder\MethodBuilder", $builder->method("name"));
    }

    public function testFindMethod()
    {
        $builder = new ClassBuilder("Test");
        $methodBuilder = $builder->findMethod("test");

        $this->assertInstanceOf("Doctrine\CodeGenerator\Builder\MethodBuilder", $methodBuilder);

        $this->assertEquals($methodBuilder, $builder->findMethod("test"));
    }
}

