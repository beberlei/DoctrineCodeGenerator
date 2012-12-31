<?php

require_once __DIR__ . "/../vendor/autoload.php";

if ( file_exists($argv[1])) {
    $code = file_get_contents($argv[1]);
} else {
    $code = "<?php\n" . $argv[1];
}

$parser = new PHPParser_Parser(new PHPParser_Lexer);
$stmts = $parser->parse($code);

var_dump($stmts);
