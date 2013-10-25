<?php
/**
 * @file
 * Parses Drupal's Advanced Help .ini file and creates page var .kit variables
 *
 * @ingroup loft_docs
 * @{
 */
require_once 'classes/IndexInterface.php';
use aklump\loft_docs\AdvancedHelpIni;

$index = new AdvancedHelpIni($argv[1]);
$vars = array(
  'classes' => array(),
);
if (($data = $index->getData()) && isset($data[$argv[2]])) {
  $vars = $data[$argv[2]];
  $vars['classes'] = array('page-' . $vars['id']);
}
$declarations = array();
$vars['classes'] = implode(' ', $vars['classes']);
foreach ($vars as $key => $value) {
  $declarations[] = "\$$key = $value";
}

// Add in additional kit vars:

// Suppress timezone warnings if they arise
$declarations[] = '$date = ' . @date('r');

// Now write the vars
print '<!--' . implode("-->\n<!--", $declarations) . "-->\n";

/** @} */ //end of group: loft_docs
