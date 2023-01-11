<?php

namespace AKlump\LoftDocs\SearchEngine;

class SearchEngineFile implements SearchEngineFileInterface {

  /**
   * @var string
   */
  private string $contents;

  /**
   * @var string
   */
  private string $filename;

  public function __construct(string $contents, string $filename) {
    $this->contents = $contents;
    $this->filename = $filename;
  }

  public function getFilename(): string {
    return $this->filename;
  }

  public function getContents(): string {
    return $this->contents;
  }
}
