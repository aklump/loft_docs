<?php
/**
 * @file
 * Defines the TipueSearch class.
 *
 * @ingroup name
 * @{
 */

namespace AKlump\LoftDocs\SearchEngine;

use AKlump\LoftLib\Code\Strings;

/**
 * Represents a TipueSearch object class.
 *
 * @brief Integrates TipueSearch with LoftDocs
 */
final class TipueSearch implements SearchEngineInterface {

  /**
   * @var \AKlump\LoftDocs\SearchEngine\PageInterface[]
   */
  private array $pages = array();

  /**
   * {@inheritdoc}
   */
  public function addPage(PageInterface $page): self {
    $key = $page->getUrl();
    $this->pages[$key] = $page;

    return $this;
  }

  public function getFiles(): array {
    $files = [];
    $content_filename = 'tipuesearch_content.js';
    $source_files = __DIR__;
    $foo = basename(__FILE__);
    foreach (scandir($source_files) as $filename) {
      $path = $source_files . '/' . $filename;
      if ('.' === $filename || '..' === $filename || !is_file($path) || in_array($filename, [
          $content_filename,
          basename(__FILE__),
          'README.md',
        ])) {
        continue;
      }
      $contents = file_get_contents($path);
      $files[] = new SearchEngineFile($contents, $filename);
    }

    $data = ['pages' => []];
    foreach ($this->pages as $page) {

      // Fallback to the title-ized URL if no title exists.
      $title = $page->getTitle();
      $url = $page->getUrl();
      if (!$title && $url) {
        $title = pathinfo($url, PATHINFO_FILENAME);
        $title = Strings::title($title);
      }

      $data['pages'][$url] = [
        'title' => $title,
        'text' => $page->getBody(),
        'tags' => implode(' ', $page->getTags()),
        'url' => $url,
      ];
    }
    ksort($data['pages']);
    $data['pages'] = array_values($data['pages']);

    $output = array();
    $output[] = "var tipuesearch = " . json_encode($data) . ";";
    $output[] = NULL;

    $contents = implode(PHP_EOL, $output);

    $files[] = new SearchEngineFile($contents, $content_filename);

    return $files;
  }

}
