<?php

namespace AKlump\LoftDocs;

use Symfony\Component\Yaml\Yaml;
use Webuni\FrontMatter\FrontMatter;

/**
 * A class to extract metadata from a source page.
 */
class PageMetaData {

  protected $page;

  protected $pageId;

  protected $sourceDirs;

  /**
   * Construct an instance.
   *
   * @param array $source_directories
   *   One or more directories where to search for files having filename (no
   *   extension) matching the page ID.  This is usually the user source
   *   directory, and sometimes includes the generated source directory as well.
   */
  public function __construct(array $source_directories) {
    $this->sourceDirs = $source_directories;
  }

  /**
   * Set the page ID or basename.
   *
   * @param $page_id
   *   The basename to a page or it's ID.
   *
   * @return $this
   */
  public function setPageId($page_id) {
    $this->page = NULL;
    $this->pageId = $page_id;

    return $this;
  }

  /**
   * Get the meta data that is hard-coded on the page.
   *
   * This will never return implied metadata like title based on a filename.
   * Only what the author has included in the source file.
   *
   * @return array
   *   The parsed and prepared metadata, e.g. tags are an array.
   */
  public function get(): array {
    $from = $this->getPageFilepath();
    $fm = new FrontMatter();

    $data = [];
    if (is_file($from) && ($this->page = file_get_contents($from))) {

      // Detect if we're using frontmatter or HTML comment.
      if ('<!--' === substr(ltrim($this->page), 0, 4)) {
        $data = [];
        $this->page = preg_replace_callback("/<!\-\-(.+?)\-\->/s", function ($matches) use (&$data) {
          $data = trim($matches[1], "\n");
          $data = Yaml::parse($data);
        }, $this->page);
      }
      else {
        $handler = $fm->parse($this->page);
        $this->page = $handler->getContent();
        $data = $handler->getData();
      }
      $data = $this->processData($data);
    }

    return $data;
  }

  /**
   * Return $string with metadata removed.
   *
   * @param $string
   *   The contents of a page with metadata header to be remove.
   *
   * @return string
   *   $string without metadata.
   */
  public function getPage(): string {
    if (is_null($this->page)) {
      $this->get();
    }

    return $this->page ?? '';
  }

  protected function processData($data) {
    $data['tags'] = $data['tags'] ?? '';
    $data['tags'] = $this->processTags($data['tags']);

    return $data;
  }

  protected function processTags($tags) {
    return explode(' ', $tags);
  }

  /**
   * Get the full path, this scans for the filename, without knowing extension.
   *
   * @return string
   *   The full path with extension.
   */
  private function getPageFilepath(): string {
    if (empty($this->pageId)) {
      throw new \RuntimeException('You must first call setPageId().');
    }

    foreach ($this->sourceDirs as $source_dir) {
      $files = array_filter(scandir($source_dir), function ($item) {
        return pathinfo($item, PATHINFO_FILENAME) === $this->pageId;
      });
      if ($files) {
        return $source_dir . '/' . reset($files);
      }
    }

    return '';
  }

}
