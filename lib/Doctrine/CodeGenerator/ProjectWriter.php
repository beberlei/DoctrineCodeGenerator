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

use PHPParser_PrettyPrinterAbstract;
use PHPParser_PrettyPrinter_Zend;

/**
 * Generate project classes/functions to file-based code.
 *
 * Uses a PHPParser PrettyPrinter to do the formatting,
 * defaults to the Zend pretty printer.
 */
class ProjectWriter
{
    private $printer;
    private $root;

    public function __construct($root, PHPParser_PrettyPrinterAbstract $printer = null)
    {
        $this->root = $root;
        $this->printer = $printer ?: new PHPParser_PrettyPrinter_Zend();
    }

    public function write(GenerationProject $project)
    {
        foreach ($project->getFiles() as $file) {
            $this->writeFile($file);
        }
    }

    private function writeFile(File $file)
    {
        $path = $this->root . "/" . $file->getPath();
        $dir  = dirname($path);

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $code = "<?php\n" . $file->prettyPrint($this->printer);
        file_put_contents($path, $code);
    }
}

