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

use PHPParser_Node;
use Doctrine\Common\EventArgs;

class GeneratorEvent extends EventArgs
{
    const onGenerateClass = 'onGenerateClass';
    const onGenerateProperty = 'onGenerateProperty';
    const onGenerateMethod = 'onGenerateMethod';
    const onGenerateGetter = 'onGenerateGetter';
    const onGenerateSetter = 'onGenerateSetter';
    const onGenerateConstructor = 'onGenerateConstructor';
    const onGenerateFunction = 'onGenerateFunction';
    const onGenerateInterface = 'onGenerateInterface';
    const onGenerateTrait = 'onGenerateTrait';
    const onGenerateParameter = 'onGenerateParameter';

    private $node;
    private $project;

    public function __construct(PHPParser_Node $node, $project = null)
    {
        $this->node = $node;
        $this->project = $project;
    }

    public function getNode()
    {
        return $this->node;
    }

    public function getProject()
    {
        return $this->project;
    }
}

