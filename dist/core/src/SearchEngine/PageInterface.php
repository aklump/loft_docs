<?php

namespace AKlump\LoftDocs\SearchEngine;

interface PageInterface {

  const DELIMITER_BEGIN = '<!--searchable_content-->';

  const DELIMITER_END = '<!--end_searchable_content-->';

  public function getUrl(): string;

  public function setUrl(string $url): void;

  public function getTitle(): string;

  public function getBody(): string;

  /**
   * Adds tags to whatever was used in the constructor.
   *
   * @param array $tags
   *
   * @return void
   */
  public function addTags(array $tags): void;

  public function getTags(): array;
}
