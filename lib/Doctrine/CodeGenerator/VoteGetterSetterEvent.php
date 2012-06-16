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

use Doctrine\Common\EventArgs;
use PHPParser_Node;

/**
 * The Generate Getter/Setter Listener asks for votes on all properties. This
 * way extensions can prevent generation of getter/setter by the simple
 * listener.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class VoteGetterSetterEvent extends EventArgs
{
    private $allowGetter = true;
    private $allowSetter = true;

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

    public function denyBoth()
    {
        $this->allowSetter = false;
        $this->allowGetter = false;
    }

    public function denyGetter()
    {
        $this->allowGetter = false;
    }

    public function denySetter()
    {
        $this->allowSetter = false;
    }

    /**
     * Get allowGetter.
     *
     * @return allowGetter.
     */
    public function getAllowGetter()
    {
        return $this->allowGetter;
    }

    /**
     * Get allowSetter.
     *
     * @return allowSetter.
     */
    public function getAllowSetter()
    {
        return $this->allowSetter;
    }
}

