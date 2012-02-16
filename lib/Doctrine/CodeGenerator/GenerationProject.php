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
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\CodeGenerator;

use PHPParser_Parser;
use PHPParser_NodeTraverser;

class GenerationProject
{
    private $root;
    private $parser;
    private $traverser;
    private $files = array();

    public function __construct($root, array $visitors = array(), PHPParser_Parser $parser = null)
    {
        $this->root = $root;
        $this->traverser = new PHPParser_NodeTraverser();
        $this->parser = new Parser($this->traverser, $parser ?: new PHPParser_Parser());
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
            if (file_exists($this->root . "/" . $path)) {
                $this->files[$path] = $this->parser->parseFile($this->root . "/" . $path);
            } else {
                $this->files[$path] = array();
            }
            $this->files[$path] = new File($path, $this->files[$path]);
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

    public function write()
    {
        $prettyPrinter = new \PHPParser_PrettyPrinter_Zend; // TODO: Configurable
        foreach ($this->files as $file) {
            $path = $this->root . "/" . $file->getPath();
            $dir = dirname($path);
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            $code = "<?php\n" . $file->prettyPrint($prettyPrinter);
            file_put_contents($path, $code);
        }
    }
}

