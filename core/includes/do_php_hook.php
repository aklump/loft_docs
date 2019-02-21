<?php

/**
 * @file
 * A wrapper for PHP hooks so that we can include libraries and set up vars.
 */

use AKlump\LoftDocs\Compiler;
use AKlump\LoftLib\Storage\FilePath;

array_shift($argv);

// Load our dependencies.
require $argv[2] . '/vendor/autoload.php';

// This should be used by the hook files.
$compiler = new Compiler(FilePath::create($argv[9]));

// Load the hook file.
require_once $argv[0];
