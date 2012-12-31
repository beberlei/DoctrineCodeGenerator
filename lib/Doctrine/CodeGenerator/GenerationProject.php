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

namespace Doctrine\CodeGenerator;

use Doctrine\CodeGenerator\Builder\ClassBuilder;

/**
 * Code Generation project holds classes and functions to generate.
 *
 * During code generation you can add and modify classes and functions
 * to be generated.
 */
class GenerationProject
{
    private $classes   = array();
    private $functions = array();

    /**
     * @return ClassBuilder
     */
    public function getClass($className)
    {
        if ( ! isset($this->classes[$className])) {
            $this->classes[$className] = new ClassBuilder($className);
        }

        return $this->classes[$className];
    }

    public function getClasses()
    {
        return $this->classes;
    }

    public function getFunction($functionName)
    {
        throw new \BadMethodCallException("not implemented yet");
    }

    public function getFiles()
    {
        $files = array();

        foreach ($this->classes as $class) {
            $path = str_replace(array("\\", "_"), "/", $class->getName()) . ".php";
            $stmts = array($class->getNode());

            if ($class->getNamespace()) {
                $stmts = array(
                    new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name($class->getNamespace()), $stmts)
                );
            }

            $files[] = new File($path, $stmts);
        }

        return $files;
    }
}

