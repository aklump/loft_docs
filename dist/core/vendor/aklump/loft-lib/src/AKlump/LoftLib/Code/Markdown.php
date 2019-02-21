<?php

namespace AKlump\LoftLib\Code;

/**
 * A class for working with Markdown.
 */
class Markdown {

  /**
   * Some markdown text.
   *
   * @var string
   */
  protected $markdown = '';

  /**
   * Markdown constructor.
   *
   * @param string $markdown
   *   Some markdown text to manipulate.
   */
  public function __construct($markdown) {
    $this->markdown = $markdown;
  }

  /**
   * Generate markdown for a table from an array.
   *
   * @param array $rows
   *   An array of associative arrays each representing a row in the table.  The
   *   keys are used as column names, unless you pass $keys.
   * @param array $keys
   *   Optional.  An array of column keys to use instead of the keys in
   *   $rows[0].
   *
   * @return string
   *   The markdown code representing a table.
   */
  public static function table(array $rows, array $keys = []) {
    $build = array();
    $build[] = empty($keys) ? ($keys = array_keys(array_values($rows)[0])) : $keys;
    $build[] = NULL;
    $build = array_merge($build, $rows);

    return array_reduce($build, function ($carry, $row) use ($keys) {
      if (is_null($row)) {
        $line = '|' . str_repeat('---|', count($keys));
      }
      else {
        $row = array_map(function ($item) {
          return is_scalar($item) ? $item : json_encode($item);
        }, $row);
        $line = '| ' . implode(' | ', str_replace('|', '\|', $row)) . ' |';
      }

      return $carry . $line . PHP_EOL;
    });
  }

  /**
   * Remove an item from a markdown list.
   *
   * @param $heading
   * @param $item
   * @param string $heading_level
   * @param string $item_bullet
   *
   * @return $this
   */
  public function removeItemFromList($heading, $item, $heading_level = '##', $item_bullet = '*') {
    $list_title = $heading_level . ' ' . $heading;
    if (strstr($this->markdown, $list_title) !== FALSE) {
      $build = array();
      $parts = explode($list_title, $this->markdown);
      $build[] = $parts[0] . rtrim($list_title);
      $build[] = NULL;

      $lines = explode(PHP_EOL, trim($parts[1]));
      $removed = FALSE;
      $ignore_rest = FALSE;
      $lines = array_filter($lines, function ($line) use ($item_bullet, $item, &$removed, &$ignore_rest) {
        if ($ignore_rest) {
          return TRUE;
        }

        if ($item_bullet . ' ' . $item === $line) {
          $removed = TRUE;

          return FALSE;
        }
        elseif ($removed && !trim($line)) {
          return FALSE;
        }
        elseif ($removed && substr($line, 0, 1) !== $item_bullet) {
          $ignore_rest = TRUE;
        }

        return TRUE;
      });

      $build[] = implode(PHP_EOL, $lines);
      $this->markdown = trim(implode(PHP_EOL, $build));
      $this->ensureHeadingPreSpacing();
    }

    return $this;
  }

  /**
   * Ensures that there are two line breaks preceding all headers.
   *
   * @return $this
   */
  public function ensureHeadingPreSpacing() {
    $this->markdown = preg_replace('/([^\n])(\n#{1,6}\s*\S)/', "\$1\n\$2", $this->markdown);

    return $this;
  }

  /**
   * Adds an item to the top of a list which is titled by a header.
   *
   * @param        $heading
   *                           The heading title without the heading markup.
   * @param string $item
   *                           The item without bullet.
   * @param string $heading_level
   * @param string $item_bullet
   *                           One of * or 1. to define the list type.
   *
   *
   * @return $this
   */
  public function addItemToList($heading, $item, $heading_level = '##', $item_bullet = '*') {
    $build = array();

    $list_title = $heading_level . ' ' . $heading;
    if (empty($this->markdown) || strstr($this->markdown, $list_title) === FALSE) {
      $build[] = rtrim($this->markdown);
      $build[] = NULL;
      $build[] = $list_title;
      $build[] = NULL;
      $build[] = $item_bullet . ' ' . $item;
    }
    else {
      $parts = explode($list_title, $this->markdown);
      $build[] = $parts[0] . rtrim($list_title);
      $build[] = NULL;
      $build[] = $item_bullet . ' ' . $item;
      $build[] = ltrim($parts[1]);
    }
    $this->markdown = trim(implode(PHP_EOL, $build));
    $this->ensureHeadingPreSpacing();

    return $this;
  }

  /**
   * Return the current value of $this->markdown.
   *
   * @return string
   *   The markdown code.
   */
  public function getMarkdown() {
    return $this->markdown;
  }

}
