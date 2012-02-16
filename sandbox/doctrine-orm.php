<?php
require_once __DIR__ . "/../vendor/.composer/autoload.php";

use Symfony\Component\Console\Application;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$config = Setup::createXMLMetadataConfiguration(array(__DIR__ . '/xml'));
$em = EntityManager::create(array('driver' => 'pdo_sqlite', 'memory' => true), $config);

$helper = new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em);
$set =  new \Symfony\Component\Console\Helper\HelperSet();
$set->set($helper, 'em');

$console = new Application();
$console->setHelperSet($set);
$console->addCommands(array(
    new Doctrine\CodeGenerator\Command\GenerateCommand(),
));
$console->run();

