# Code Generation

In general code-generation just moves the inability to correctly abstract code into a layer that simplifies the downsides of writing lots of code by hand. However there are use-cases for code-generation:

* Transforming existing models (XML, UML, CSV, whatever) from one to another representation.
* Generating boiler-plate code that is impossible to abstract nicely (`__get`/`__set`/`__call` alternatives)
* Generating small bits of repetitive code that is not abstractable in PHP like specialized getters/setters, bidirectional method handlers.

However the current approach for code-generation in Doctrine is fail. A single class organizes the code generation and has no sane means for extension. Templating based code-generation mechanism come into mind, but they only make the problem larger. Templates are not useful for code-generation because they dont offer manipulation and composition of different concerns, they only allow a linear direction of templates that are stacked on top of each other.

Proper Code-Generation uses an AST that can be manipulated by the developer at any given point during the process. Manipulation of the AST can be triggered by events. The code-generator is feed by multiple sources of input that all operate on a large AST. The first source builds the general class layout, the second one adds to it and so on. The order of input given to the AST generator is relevant. It should be possible to write compiling/running code and then link this into the generated source code as if you literally write "traits". This should feel sort of like aspect-oriented programming where you designate some piece of code to be "used" by the code-generator during the generation process.

The problem of code-generators is that they leave you with thousands of lines of untested code that is to be integrated in your application. This often leaves you with a huge gap of tested to untested code that is impossible to close.

## Idea

Using [nikics PHP Parser](https://github.com/nikic/PHP_Parser) library we generate code using an AST. The code is generated from a set of input sources. Events are triggered when code blocks are generated:

* Class
* Property
* Method
    * GetterMethod
    * SetterMethod
    * Constructor
* Function

Event Handlers can register to each events and either:

* Manipulate the AST
* Trigger more specialized events

A configuration for the code-generator would look like:

    @@@ yml

    generator:
      input:
        doctrine-mapping: from-database
      events:
        Doctrine\CodeGenerator\EventHandler\GetterSetterListener: ~
        Doctrine\CodeGenerator\EventHandler\ReadOnlyEntityValueObjectListener: ~
        Doctrine\CodeGenerator\EventHandler\BidirectionalAssociationListener: ~
        Doctrine\CodeGenerator\EventHandler\DocBlockListener: ~
        Doctrine\CodeGenerator\EventHandler\DoctrineAnnotations: ~
        Doctrine\CodeGenerator\EventHandler\ImmutableObjects:
          - "ImmutableClass1"
      output:
        codingStandard: Symfony

PHP Parser does not provide a nice API to manipulate the AST (yet). Because this is a major operation we need to create an API (and contribute it back!) for this. A managable solution for developers would be a kind of DOM like approach with a jQuery-like API for manipulation. A selector language can filter for specific code elements and then manipulate them:

    $builder = $project->createEmptyFileForClass('Test'); // Assume PSR-0
    $builder->class('Test') // class Test
            ->property('foo')->private()->docblock('My property') // /** My Property */ private $foo;
            ->property('bar') // public $bar;
            ->method('setFoo')->param('foo')->typehint('SplString')->default(null) // function setFoo(SplString $foo = null)
            ->code(
                $builder->assignment(
                    $builder->instanceVariable('foo'), $builder->variable('foo') // $this->foo = $foo
                )
            )->append($builder->return($builder->instanceVariable('foo'))) // return $this->foo
            ->method('

    $tree->find('Class[name="ImmutableClass"] Property[name="set*"]')->remove();
    $returnStmt = $tree->find("Return");
    $returnStmt->before("$this->assertFoo();");

As you can see in the last block there is also support for injecting strings into the AST. Using PHP Parser these bits are parsed into an AST aswell and put at the specific locations in the AST.


