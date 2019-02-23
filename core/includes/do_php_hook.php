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
$outline_file = FilePath::create($argv[4], ['install' => FALSE]);
$compiler = new Compiler(FilePath::create($argv[1]), FilePath::create($argv[9]), $outline_file);

// Load the hook file.
require_once $argv[0];
