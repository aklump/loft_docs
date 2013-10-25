<?php
/**
 * @file
 * Compile all .kit files a la CodeKit
 *
 * @ingroup loft_docs
 * @{
 */

require_once 'vendor/autoload.php';
use aklump\kit_php\Compiler;
use aklump\loft_parser\HTMLTagRemoveAction;

// Convert paths to images to include @page
if (isset($argv[1]) && isset($argv[2])) {

  $obj = new Compiler($argv[1], $argv[2]);
  $obj->apply();

  // Remove additional h1 tags from files; we make a general assumption that the
  // tpl header will include an h1 tag, and that if there is another one it has
  // been provided in the source page and should be supressed.
  $parser = new HTMLTagRemoveAction('h1', 1);
  foreach ($obj->getCompiledFiles() as $path) {
    if (($contents = file_get_contents($path))
        && $parser->parse($contents)
        && ($fp = fopen($path, 'w'))) {
      fwrite($fp, $contents);
      fclose($fp);
    }
  }
}

/** @} */ //end of group: loft_docs
