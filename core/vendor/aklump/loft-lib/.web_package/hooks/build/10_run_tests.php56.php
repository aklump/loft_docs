<?php

/**
 * @file
 * Run the automated tests.
 */

namespace AKlump\WebPackage;

$build
  ->setPhp('/Applications/MAMP/bin/php/php5.6.32/bin/php')
  ->setPhpUnit('/Users/aklump/opt/phpunit/phpunit-5.phar')
  ->runTests('phpunit.v5.xml')
  ->displayMessages();
