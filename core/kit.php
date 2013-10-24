<?php
/**
 * @file
 * Compile all .kit files a la CodeKit
 *
 * @ingroup loft_docs
 * @{
 */

require_once 'vendor/autoload.php';

// Convert paths to images to include @page
if (isset($argv[1]) && isset($argv[2])) {
  $obj = new aklump\kit_php\Compiler($argv[1], $argv[2]);
  $obj->apply();
}

/** @} */ //end of group: loft_docs
