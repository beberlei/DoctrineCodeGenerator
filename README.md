# Doctrine Code Generator

In general code-generation just moves the inability to correctly abstract code into a layer that simplifies the downsides of writing lots of code by hand. However there are use-cases for code-generation, mostly in context with Object-Relational-Mappers:

* Transforming existing models (XML, UML, CSV, whatever) from one to another representation.
* Generating boiler-plate code that is impossible to abstract nicely (`__get`/`__set`/`__call` alternatives)
* Generating small bits of repetitive code that is not abstractable in PHP like specialized getters/setters, bidirectional method handlers.

However the current approach for code-generation in Doctrine is fail. A single class organizes the code generation and has no sane means for extension. Templating based code-generation mechanism come into mind, but they only make the problem larger. Templates are not useful for code-generation because they dont offer manipulation and composition of different concerns, they only allow a linear direction of templates that are stacked on top of each other.

Proper Code-Generation uses an AST that can be manipulated by the developer at any given point during the process. Manipulation of the AST can be triggered by events. The code-generator is feed by multiple sources of input that all operate on a large AST. The first source builds the general class layout, the second one adds to it and so on. The order of input given to the AST generator is relevant. It should be possible to write compiling/running code and then link this into the generated source code as if you literally write "traits". This should feel sort of like aspect-oriented programming where you designate some piece of code to be "used" by the code-generator during the generation process.

The problem of code-generators is that they leave you with thousands of lines of untested code that is to be integrated in your application. This often leaves you with a huge gap of tested to untested code that is impossible to close.

## Idea

Using [nikics PHP Parser](https://github.com/nikic/PHP_Parser) library we generate code using an AST. The code is generated from a set of input sources, usually during the "onStartGeneration" event. Events are subsequently triggered when code blocks are generated:

* StartGeneration
* Class
* Property
* Method
    * GetterMethod
    * SetterMethod
    * Constructor
* Function

Event Handlers can register to all events and for example:

* Manipulate the AST
* Trigger more specialized events

A configuration for the code-generator would look like:

    generator:
      destination: "code"
      events:
        Doctrine\CodeGenerator\Listener\ORM\GenerateProjectListener: ~
        Doctrine\CodeGenerator\Listener\GetterSetterListener: ~
        Doctrine\CodeGenerator\Listener\ReadOnlyEntityValueObjectListener: ~
        Doctrine\CodeGenerator\Listener\BidirectionalAssociationListener: ~
        Doctrine\CodeGenerator\Listener\DocBlockListener: ~
        Doctrine\CodeGenerator\Listener\DoctrineAnnotations: ~
        Doctrine\CodeGenerator\Listener\ImmutableObjects:
          - "ImmutableClass1"
        Doctrine\CodeGenerator\Listener\ORM\MappingGenerator:
            type: xml
      output:
        codingStandard: Symfony

## Generating Code

This project provides a bunch of builder objects on top of the PHP Parser Builder API. The aim is to have a fluent and
convenient API to generate code.

