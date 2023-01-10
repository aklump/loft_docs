<?php

namespace AKlump\LoftDocs\SearchEngine;

interface SearchEngineInterface {

  /**
   * Adds a page to the index.
   *
   * @param \AKlump\LoftDocs\SearchEngine\PageInterface $page
   *
   * @return $this
   */
  public function addPage(PageInterface $page): self;

  /**
   * @return \AKlump\LoftDocs\SearchEngine\SearchEngineFileInterface[]
   */
  public function getFiles(): array;
}
