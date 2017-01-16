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

$vars = array(
    'classes' => array(),
);
list(, $outline_file, $page_id, $vars['version']) = $argv;
$g = new Data();
$outline = load_outline($outline_file);
$index = new Index($outline);

$vars['index'] = array();
foreach ($index->getData() as $key => $value) {
    // Skip a self reference
    if (in_array($key, array('index', 'search--results'))) {
        continue;
    }
    $vars['index'][] = $value;
}

if (($data = $index->getData()) && isset($data[$page_id])) {
    $vars += $data[$page_id];
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

// Search support
$g->onlyIf($outline, 'settings.search')->set($vars, 'search', true);

$json = json_encode($vars);
print $json;
