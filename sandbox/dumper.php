<?php

require_once __DIR__ . "/../vendor/autoload.php";

$code = "<?php\n" . $argv[1];
$parser = new PHPParser_Parser(new PHPParser_Lexer);
$stmts = $parser->parse($code);

var_dump($stmts);
