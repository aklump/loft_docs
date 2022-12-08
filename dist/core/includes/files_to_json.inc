<?php
/**
 * @file
 * Reads the filesystem and writes new .json format as a file.
 */

use AKlump\Data\Data;
use AKlump\LoftDocs\Compiler;
use AKlump\LoftDocs\PageMetaData;
use AKlump\LoftLib\Bash\Color;

$file = __FILE__;
require_once dirname($file) . '/../vendor/autoload.php';

$source_dir = $argv[1];
$generated_dir = $argv[2];
$json_file = $argv[3];
$merge_file = $argv[4];

if (count($argv) < 3  || empty($source_dir) || empty($json_file)) {
  echo "Missing parameters to $file" . PHP_EOL;

  return;
}

if (file_exists($json_file)) {
  echo "Cannot create $json_file as it already exists." . PHP_EOL;

  return;
}

$info = array();

// Get all the files to index from two directories.
$user = Compiler::indexSourceFiles($source_dir);
$generated = Compiler::indexSourceFiles($generated_dir);
$first_level = array_merge($user, $generated);

$g = new Data();
foreach ($first_level as $data) {
  if (empty($data['filename'])) {
    continue;
  }
  $path = $data['path'];
  $file = $data['basename'];
  $front_matter = [];
  $contents = '';
  $body = '';
  if (is_file($path) && ($contents = file_get_contents($path))) {
    $page_metadata = new PageMetaData([$data['dirname']]);
    $page_metadata->setPageId($data['filename']);
    $front_matter = $page_metadata->get();
    $body = $page_metadata->getPage();
  }

  // Allow this file to be skipped for indexing.
  if ($g->get($front_matter, 'noindex')) {
    continue;
  }

  $meta = array();

  //
  // 1. Frontmatter
  $meta['id'] = $g->get($front_matter, 'id');
  $meta['chapter'] = $g->get($front_matter, 'chapter');
  $meta['section'] = $g->get($front_matter, 'section');
  $meta['title'] = $g->get($front_matter, 'title');

  //
  // 2. html heading
  if (preg_match('/^#(?!#)\s*(.+)$/m', $body, $matches)) {
    $g->fill($meta, 'title', $matches[1]);
    $g->fill($meta, 'content_title', $matches[1]);
  }
  else {
    $g->fill($meta, 'content_title', $meta['title']);
  }

  //
  // 3. file-name derived meta
  //
  // We check for chapter--section.md format
  if (($parts = explode('--', $file)) && count($parts) > 1) {
    $g->fill($meta, 'chapter', array_shift($parts));
    $g->fill($meta, 'section', implode('', $parts));
  }

  $g->fill($meta, 'title', clean_title($file));
  $g->fill($meta, 'content_title', clean_title($file));

  // In the top level there is no chapter indication.
  if (Compiler::pathIsSection($file)) {
    $info[$data['filename_compiled']] = array(
      'file' => $data['filename_compiled'],
      'id' => $g->get($meta, 'id'),
      'title' => $g->get($meta, 'title'),
      'content_title' => $g->get($meta, 'content_title'),
      'parent' => $g->get($meta, 'chapter'),
    );
  }

  // Place these in alphabetical order by title.  Later in json.inc the merge
  // file is going to allow order override when it's merged in, but by default
  // we want them in title ASC order.
  uasort($info, function ($a, $b) {
    return strcasecmp($a['title'], $b['title']);
  });
}

$json_status = TRUE;
require_once dirname(__FILE__) . '/json.inc';
if ($json_status !== TRUE) {
  echo Color::wrap('red', $json_status->getMessage());
  exit(1);
}
exit(0);

