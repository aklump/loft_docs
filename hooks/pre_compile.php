<?php
/**
 * @file
 * An example of a pre-compile php file
 *
 * @ingroup loft_docs
 * @{
 */

$contents = "## Today is: " . date('r');
echo $compiler->addInclude('_headline.md', $contents)
    ->getBasename() . ' has been created.' && exit(0);
exit(1);
