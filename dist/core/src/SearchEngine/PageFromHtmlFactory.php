<?php

namespace AKlump\LoftDocs\SearchEngine;

/**
 * Parses an HTML page string into a \AKlump\LoftDocs\SearchEngine\Page
 *
 * The resulting object can be used with the search engine.
 *
 * The tags come from the metatag keywords.
 * The title comes from the first h1 or h2 found.
 * The content comes from inside the  PageInterface::DELIMITER_* wrapper.
 *
 * @code
 * $page_factory = new PageFromHtmlFactory();
 * $html = file_get_contents('/foo/bar.html');
 * $page = $page_factory($html);
 * @endcode
 */
final class PageFromHtmlFactory {

  private $titleRegex = '/<h1*?>(.+?)<\/h1>|<h2*?>(.+?)<\/h2>/si';

  public function __invoke($html_string): PageInterface {
    $tags = $this->extractTags($html_string);
    $title = $this->extractTitle($html_string);
    $contents = $this->extractContent($html_string);

    return new Page('', $title, $contents, $tags);
  }

  private function extractTags(string $html): array {
    if (preg_match_all('/<meta.+?>/is', $html, $matches)) {
      foreach ($matches[0] as $metatag) {
        $metatag = simplexml_load_string($metatag);
        if (strval($metatag->attributes()['name']) === 'keywords') {
          return explode(',', $metatag->attributes()['content'] ?? '');
        }
      }
    }

    return [];
  }

  private function extractTitle(string $html): string {
    preg_match($this->titleRegex, $html, $matches);

    return $matches[1] ?? '';
  }

  private function extractContent(string $html): string {

    $processed = $html;
    $processed = preg_replace($this->titleRegex, '', $processed);

    $regex = implode('', [
      '/',
      preg_quote(PageInterface::DELIMITER_BEGIN, '/'),
      '(.+?)',
      preg_quote(PageInterface::DELIMITER_END, '/'),
      '/s',
    ]);
    if (preg_match($regex, $processed, $matches)) {
      $processed = $matches[1];
    }
    $processed = strip_tags($processed);
    $processed = trim($processed);

    return $processed;
  }

}
