<?php
/**
 * @file
 * Parses Drupal's Advanced Help .ini file to create the directory or index
 *
 * @ingroup loft_docs
 * @{
 */
require_once dirname(__FILE__) . '/classes/IndexInterface.php';
$index = new aklump\loft_docs\AdvancedHelpIni($argv[1]);

$list = array();
foreach ($index->getData() as $key => $value) {
  // Skip a self reference
  if ($key == 'index') {
    continue;
  }
  $list[] = '<a href="' . $value['file'] . '">' . $value['title'] . '</a>';
}

$tpl_dir = $argv[2];
$list = implode("</li>\n<li>", $list);
$output = <<<EOD
<!-- @include ../$tpl_dir/header.kit -->
<ul class="index"><li>{$list}</li></ul>
<!-- @include ../$tpl_dir/footer.kit -->
EOD;

print $output;

/** @} */ //end of group: loft_docs
