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

namespace Doctrine\CodeGenerator\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Command\Command;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('configfile', InputArgument::OPTIONAL, 'The generator.yml file path'),
                new InputOption('xml', null, InputOption::VALUE_NONE, 'To output help as XML'),
                new InputOption('raw', null, InputOption::VALUE_NONE, 'To output raw command list')
            ))
            ->setName('code:generate')
            ->setDescription('Generate code')
            ->setHelp(<<<EOF
The <info>code:generator</info> command generates php code from the specified generator.yml
project description.

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = realpath($input->getArgument('configfile'));
        $config = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($path));

        if (!isset($config['generator'])) {
            throw new \RuntimeException;
        }

        $destination = realpath(dirname($path)) . "/". $config['generator']['destination'];
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }

        $sourceClass = $config['generator']['source']['class'];
        $source = new $sourceClass($config['generator']['source']['arguments']);

        $code = new \Doctrine\CodeGenerator\Builder\CodeBuilder;
        $evm = new \Doctrine\Common\EventManager;
        foreach ($config['generator']['listeners'] as $listener => $args) {
            if (!is_subclass_of($listener, 'Doctrine\CodeGenerator\Listener\AbstractCodeListener')) {
                throw new \RuntimeException("Listener $listener has to extend AbstractCodeListener");
            }
            $listener = new $listener($args);
            $listener->setCodeBuilder($code);
            $evm->addEventSubscriber($listener);
        }
        $parent = new \Doctrine\CodeGenerator\Visitor\ParentVisitor();
        $eventvisitor = new \Doctrine\CodeGenerator\Visitor\EventsVisitor($evm, $parent);
        $visitors = array($parent, $eventvisitor);

        $project = new \Doctrine\CodeGenerator\GenerationProject($destination, $visitors);
        $source->generate($project);

        $lc = 0;
        do {
            $project->traverse();
            $lc++;
        } while($eventvisitor->getCounter() > 0 && $lc < 100);
        $project->write();
    }
}

