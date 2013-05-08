<?php
/**
 * @file
 * Parses Drupal's Advanced Help .ini file to create the directory or index
 *
 * @ingroup ovadata_docs OvaData Developer Docs
 * @{
 */
require_once 'classes/IndexInterface.php';
$index = new AdvancedHelpIni($argv[1]);

$list = array();
foreach ($index->getData() as $value) {
  $list[] = '<a href="' . $value['file'] . '">' . $value['title'] . '</a>';
}

$tpl_dir = $argv[2];
$list = implode("</li>\n<li>", $list);
$output = <<<EOD
<!-- @include ../$tpl_dir/header.kit -->
<ul><li>{$list}</li></ul>
<!-- @include ../$tpl_dir/footer.kit -->
EOD;

print $output;

/** @} */ //end of group: ovadata_docs
