<?php
/**
 * @file
 * Parses Drupal's Advanced Help .ini file and creates page var .kit variables
 *
 * @ingroup ovadata_docs OvaData Developer Docs
 * @{
 */
require_once 'classes/IndexInterface.php';
$index = new AdvancedHelpIni($argv[1]);
if (($data = $index->getData()) && isset($data[$argv[2]])) {
  $vars = $data[$argv[2]];
  $vars['classes'] = array($vars['id']);
}
$declarations = array();
$vars['classes'] = implode(' ', $vars['classes']);
foreach ($vars as $key => $value) {
  $declarations[] = "\$$key = $value";
}
print '<!--' . implode("-->\n<!--", $declarations) . "-->\n";

/** @} */ //end of group: ovadata_docs
