<?php
require_once __DIR__ . "/../vendor/.composer/autoload.php";

use Symfony\Component\Console\Application;

$console = new Application();
$console->addCommands(array(
    new Doctrine\CodeGenerator\Command\GenerateCommand(),
));
$console->run();

