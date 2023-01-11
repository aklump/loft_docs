<?php
/**
 * @file
 * Defines the Page class
 */

namespace AKlump\LoftDocs\SearchEngine;

class Page implements PageInterface {

  /**
   * @var string
   */
  protected $url;

  /**
   * @var string
   */
  protected $title;

  /**
   * @var string
   */
  protected $body;

  /**
   * @var array
   */
  protected $tags = [];

  /**
   * Constructor
   *
   * @param string $url
   * @param string $title
   * @param string $body
   * @param array $tags
   */
  function __construct(string $url = '', string $title = '', string $body = '', array $tags = []) {
    $this->setUrl($url);
    $this->title = $title;
    $this->body = $body;
    $this->setTags($tags);
  }

  private function setTags(array $tags): void {
    $tags = array_values(array_filter(array_unique($tags)));

    // Trim tags and then look for internal spaces.
    foreach ($tags as &$tag) {
      $tag = trim($tag);
      if (strpos($tag, ' ') !== FALSE) {
        throw new \InvalidArgumentException("A single tag \"$tag\" may not contain spaces.");
      }
    }
    $this->tags = $tags;
  }

  /**
   * {@inheritdoc}
   */
  public function getUrl(): string {
    return $this->url;
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle(): string {
    return $this->title;
  }

  /**
   * {@inheritdoc}
   */
  public function getBody(): string {
    return $this->body;
  }

  /**
   * {@inheritdoc}
   */
  public function addTags(array $tags): void {
    $tags = array_merge($this->getTags(), $tags);
    $this->setTags($tags);
  }

  /**
   * {@inheritdoc}
   */
  public function getTags(): array {
    return $this->tags;
  }

  public function setUrl(string $url): void {
    $this->url = trim($url, '/');
  }
}
