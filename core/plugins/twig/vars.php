<?php
/**
 * @file
 * Parses Drupal's Advanced Help .ini file and creates page var .kit variables
 *
 * @ingroup loft_docs
 * @{
 */
use AKlump\Data\Data;
use AKlump\LoftDocs\OutlineJson as Index;

$CORE = getenv('LOFT_DOCS_CORE');

require_once $CORE . '/vendor/autoload.php';

$g = new Data();
$outline = load_outline($argv[1]);
$index = new Index($outline);

$vars = array(
    'classes' => array(),
);

$vars['index'] = array();
foreach ($index->getData() as $key => $value) {
    // Skip a self reference
    if ($key == 'index') {
        continue;
    }
    $vars['index'][] = $value;
}

if (($data = $index->getData()) && isset($data[$argv[2]])) {
    $vars = $data[$argv[2]];
    $vars['classes'] = array('page--' . $vars['id']);
}

// Ensure these default vars
$g->ensure($vars, 'title', '');
$g->ensure($vars, 'prev', 'javascript:void(0)');
$g->ensure($vars, 'prev_id', '');
$g->ensure($vars, 'prev_title', '');
$g->ensure($vars, 'next', 'javascript:void(0)');
$g->ensure($vars, 'next_id', '');
$g->ensure($vars, 'next_title', '');

// Add in additional vars:
$now = new \DateTime('now', new \DateTimeZone('America/Los_Angeles'));
$vars['date'] = $now->format('r');

$vars['version'] = $argv[3];

// Search support
if (!empty($outline['settings']['search'])) {
    $declarations[] = '$search = true';
    if ($argv[2] === 'search--results') {
        $vars['search_results_page'] = true;
    }
    else {
        $vars['search_results_page'] = false;
    }
}
else {
    $vars['search'] = false;
    $vars['search_results_page'] = false;
}


$json = json_encode($vars);
print $json;
