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
use Doctrine\Common\EventSubscriber;
use Doctrine\CodeGenerator\Builder\ClassBuilder;
use Doctrine\CodeGenerator\Builder\StmtBuilder;

class TimestampableListener implements EventSubscriber
{
    private $classes;

    public function __construct(array $classes = array())
    {
        $this->classes = $classes;
    }

    public function onGenerateClass(GeneratorEvent $event)
    {
        $class = $event->getNode();
        if ( !in_array($class->name, $this->classes)) {
            return;
        }

        $code = new StmtBuilder();
        $builder = new ClassBuilder($class);
        $builder->appendProperty('updated')
                ->appendProperty('created')
                ->findMethod('__construct')
                    ->append(
                        $code->assignment(
                            $code->instanceVariable('created'),
                            $code->instantiate('DateTime')
                        )
                    )
                ->end()
                ->append($code->classCode(<<<ETS
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
                ))
            ;
    }

    public function getSubscribedEvents()
    {
        return array('onGenerateClass');
    }
}

