<?php

namespace AKlump\LoftDocs;

use Webuni\FrontMatter\FrontMatter;

/**
 * A class to extract metadata from a source page.
 */
class PageMetaData {

  protected $pageId;

  protected $source;

  public function __construct(\AKlump\LoftLib\Storage\FilePath $source_dir) {
    $this->source = $source_dir;
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
    if (($contents = file_get_contents($from))) {
      $document = $fm->parse($contents);
    }

    return $this->processData($document->getData());
  }

  protected function processData($data) {
    $data['tags'] = $data['tags'] ?? '';
    $data['tags'] = $this->processTags($data['tags']);

    return $data;
  }

  protected function processTags($tags) {
    return explode(' ', $tags);
  }

  private function getPageFilepath(): string {
    if (empty($this->pageId)) {
      throw new \RuntimeException('You must first call setPageId().');
    }
    $filepath = exec(sprintf('cd "%s" && ls "%s"*', $this->source->getPath(), $this->pageId));

    return $this->source->getPath() . '/' . $filepath;
  }

}
