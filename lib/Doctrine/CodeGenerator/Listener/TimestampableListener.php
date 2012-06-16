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

namespace Doctrine\CodeGenerator\Listener;

use Doctrine\CodeGenerator\GeneratorEvent;
use Doctrine\CodeGenerator\Builder\Manipulator;

class TimestampableListener extends AbstractCodeListener
{
    private $classes;

    public function __construct(array $classes = array())
    {
        $this->classes = $classes;
    }

    public function onGenerateClass(GeneratorEvent $event)
    {
        $code  = $this->code;
        $class = $event->getNode();

        if ( ! in_array($class->name, $this->classes)) {
            return;
        }

        $this->makeTimestampable($class, $code);
    }

    public function makeTimestampable($class, $code)
    {
        $manipulator = new Manipulator();
        $constructor = $manipulator->findMethod($class, '__construct');

        $updatedProperty = $code->property('updated')->makeProtected()->getNode();
        $createdProperty = $code->property('created')->makeProtected()->getNode();

        $updatedProperty->props[0]->setAttribute('type', 'DateTime');
        $createdProperty->props[0]->setAttribute('type', 'DateTime');

        $manipulator->addProperty($class, $createdProperty);
        $manipulator->addProperty($class, $updatedProperty);

        $manipulator->append(
                $constructor,
            $code->assignment(
                $code->instanceVariable('created'),
                $code->instantiate('DateTime')
            )
        )->append($class, $code->classCode(<<<ETS
public function getCreated()
{
    return \$this->created;
}

public function setUpdated(\DateTime \$date)
{
    \$this->updated = \$date;
}

public function getUpdated()
{
    return \$this->updated;
}
ETS
            )
        );
    }
}

