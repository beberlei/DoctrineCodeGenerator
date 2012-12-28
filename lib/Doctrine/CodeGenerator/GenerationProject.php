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

use PHPParser_Parser;
use PHPParser_Lexer;
use PHPParser_NodeTraverser;

class GenerationProject
{
    private $root;
    private $parser;
    private $traverser;
    private $files = array();
    private $printer;

    public function __construct(array $visitors = array())
    {
        $this->traverser = new PHPParser_NodeTraverser();
        $this->parser = new Parser($this->traverser, new PHPParser_Parser(new PHPParser_Lexer));

        foreach ($visitors as $visitor) {
            $this->traverser->addVisitor($visitor);
        }
    }

    public function getEmptyClass($className)
    {
        $path = str_replace(array("\\", "_"), "/", $className) . ".php";
        return $this->getEmptyFile($path);
    }

    public function getEmptyFile($path)
    {
        $this->files[$path] = new File($path, array());
        return $this->files[$path];
    }

    public function getFile($path)
    {
        if ( ! isset($this->files[$path])) {
            $this->files[$path] = new File($path, array());
        }

        return $this->files[$path];
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function traverse()
    {
        foreach ($this->files as $file) {
            $file->traverse($this->traverser);
        }
    }
}

