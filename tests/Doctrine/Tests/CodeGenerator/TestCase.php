<?php

namespace Doctrine\Tests\CodeGenerator;

use PHPParser_PrettyPrinter_Zend;

class TestCase extends \PHPUnit_Framework_TestCase
{
    public function assertCodeStartsWith($startsWith, $node, $message = null)
    {
        if (!is_array($node)) {
            $node = array($node);
        }

        $printer = new PHPParser_PrettyPrinter_Zend();
        $actual = $printer->prettyPrint($node);

        $this->assertStringStartsWith($startsWith, $actual, $message);
    }

    public function assertCode($expected, $node, $message = null)
    {
        if (!is_array($node)) {
            $node = array($node);
        }

        $printer = new PHPParser_PrettyPrinter_Zend();
        $actual = $printer->prettyPrint($node);

        $this->assertEquals($expected, $actual, $message);
    }
}

