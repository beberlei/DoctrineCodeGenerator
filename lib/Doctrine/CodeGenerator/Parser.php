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

use Doctrine\Common\EventManager;
use PHPParser_Parser;
use PHPParser_Lexer;
use PHPParser_NodeTraverser;

/**
 * Parser Extension
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class Parser
{
    /**
     * @var \PHPParser_Parser
     */
    private $parser;

    private $traverser;

    public function __construct(PHPParser_NodeTraverser $traverser = null, PHPParser_Parser $parser = null)
    {
        $this->parser = $parser ?: new PHPParser_Parser(new PHPParser_Lexer);
        $this->traverser = $traverser ?: new PHPParser_NodeTraverser();
    }

    public function parseString($code)
    {
        $stmts = $this->parser->parse(new PHPParser_Lexer($code));
        $this->traverser->traverse($stmts);
        return $stmts;
    }

    public function parseFile($path)
    {
        return $this->parseString(file_get_contents($path));
    }
}

