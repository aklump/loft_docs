<?php
/**
 * @file
 * Adds search to the output
 */

namespace AKlump\LoftDocs;

use AKlump\LoftDocs\SearchEngine\PageFromHtmlFactory;
use Webuni\FrontMatter\FrontMatter;
use AKlump\LoftDocs\SearchEngine\TipueSearch;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

$webpage_dir = $argv[4] ?? NULL;
$outline = $argv[1] ?? NULL;
$outline = load_outline($outline);

if (!empty($outline['settings']['search'])) {
  if ('tipuesearch' !== $outline['settings']['search']) {
    throw new \RuntimeException(sprintf('Invalid search engine: %s', $outline['settings']['search']));
  }

  $page_factory = new PageFromHtmlFactory();
  $search_engine_dir = "$webpage_dir/search";
  $search_engine_builder = new TipueSearch();

  $html_files_to_index = array_filter(scandir($webpage_dir), function ($value) {

    return preg_match('/\.html$/', $value)
      && pathinfo($value, PATHINFO_FILENAME) != 'index';
  });

  try {
    foreach ($html_files_to_index as $html_file) {
      $record = getOutlineRecordByHtmlFile($html_file);
      if (isset($record['search']) && 'noindex' === $record['search']) {
        continue;
      }

      $front_matter = getFrontMatterFromHtmlFile($html_file);
      if (isset($front_matter['search']) && 'noindex' === $front_matter['search']) {
        continue;
      }

      $html = file_get_contents($webpage_dir . '/' . $html_file);
      $page = $page_factory($html);
      $page->setUrl($html_file);
      $search_engine_builder->addPage($page);
    }
    if (!is_dir($search_engine_dir)
      && !mkdir($search_engine_dir, 0755, TRUE)) {
      throw new \RuntimeException(sprintf('Cannot create search directory: %s', $search_engine_dir));
    }
    if (!is_writeable($search_engine_dir)) {
      throw new \RuntimeException(sprintf('Search directory is not writeable: %s', $search_engine_dir));
    }
    foreach ($search_engine_builder->getFiles() as $file) {
      $path = $search_engine_dir . '/' . $file->getFilename();
      $fh = fopen($path, 'w');
      if (fwrite($fh, $file->getContents()) === FALSE) {
        throw new \RuntimeException("Unable to write file: $path");
      }
      fclose($fh);
    }
  }
  catch (\Exception $e) {
    print $e;
  }
}

function getOutlineRecordByHtmlFile($html_file) {
  global $outline;
  foreach ($outline['sections'] as $item) {
    if ($item['file'] === $html_file) {
      return $item;
    }
  }

  return [];
}

/**
 * Uses the filename of the html file and tries to find a markdown source file,
 * parses frontmatter and returns it.
 *
 * @param string $html_file
 *
 * @return array
 */
function getFrontMatterFromHtmlFile($html_file) {
  global $argv, $outline;
  $markdown_source = $argv[5];
  $md_file = pathinfo($html_file, PATHINFO_FILENAME);
  $exists = FALSE;
  foreach (get_markdown_extensions() as $ext) {
    $filename = $markdown_source . '/' . $md_file . '.' . $ext;
    if (file_exists($filename)) {
      $exists = TRUE;
      break;
    }
  }

  $front_matter = array();
  if ($exists && ($contents = file_get_contents($filename))) {
    $fm = new FrontMatter();
    $document = $fm->parse($contents);
    $front_matter = $document->getData();
  }

  // Now look for it in the outline
  if (!empty($outline['frontmatter'][$html_file])) {
    $front_matter = $outline['frontmatter'][$html_file] + $front_matter;
  }

  return $front_matter;
}

