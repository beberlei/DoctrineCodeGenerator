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

namespace Doctrine\Tests\CodeGenerator\Builder;

use PHPParser_Node_Stmt_Class;
use PHPParser_Node_Stmt_ClassMethod;
use PHPParser_Node_Stmt_Property;
use PHPParser_Builder_Property;
use Doctrine\CodeGenerator\Builder\Manipulator;
use Doctrine\CodeGenerator\Builder\CodeBuilder;
use Doctrine\Tests\CodeGenerator\TestCase;

class ManipulatorTest extends TestCase
{
    public function testSetDocComment()
    {
        $class       = new PHPParser_Node_Stmt_Class("Test");
        $manipulator = new Manipulator();

        $manipulator->setDocComment($class, "Test!");
        $manipulator->setDocComment($class, "Test1234!");

        $this->assertEquals("Test1234!", $class->getDocComment()->getText());
    }
}

