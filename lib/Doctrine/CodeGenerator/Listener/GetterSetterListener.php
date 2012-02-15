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

class GetterSetterListener implements \Doctrine\Common\EventSubscriber
{
    public function onGenerateProperty(GeneratorEvent $event)
    {
        $node = $event->getNode();
        $node->type = 2; // set protected

        $class = $event->getParent($node);

        foreach ($node->props as $property) {
            $getStmts = new \PHPParser_Node_Stmt_Return(
                new \PHPParser_Node_Expr_PropertyFetch(new \PHPParser_Node_Expr_Variable('this'), $property->name));
            $class->stmts[] = new \PHPParser_Node_Stmt_ClassMethod('set'.ucfirst($property->name));
            $class->stmts[] = new \PHPParser_Node_Stmt_ClassMethod('get'.ucfirst($property->name), array('stmts' => array($getStmts)));
        }
        var_dump($class);
    }

    public function getSubscribedEvents()
    {
        return array('onGenerateProperty');
    }
}

