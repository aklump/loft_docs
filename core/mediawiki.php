<?php
/**
 * @file
 * Provides conversion of html to mediawiki
 *
 * @ingroup loft_docs
 * @{
 */

require_once 'vendor/autoload.php';

// Convert paths to images to include @page
if (isset($argv[1])) {
  $p = new aklump\loft_docs\MediaWikiParser($argv[1], TRUE);
  $output = $p->parse();
}
print $output;

/** @} */ //end of group: loft_docs
