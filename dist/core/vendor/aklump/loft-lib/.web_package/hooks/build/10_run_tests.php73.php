<?php

/**
 * @file
 * Run the automated tests.
 */

namespace AKlump\WebPackage;

$build
  ->setPhp('/Applications/MAMP/bin/php/php7.3.7/bin/php')
  ->setPhpUnit('/Users/aklump/opt/phpunit/phpunit-6.phar')
  ->runTests('phpunit.xml')
  ->displayMessages();
