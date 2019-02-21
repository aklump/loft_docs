<?php

namespace AKlump\LoftLib\Bash;

/**
 * Handle output in the CLI.
 *
 * All methods must return a string which includes a final EOL.
 */
class Output {

  /**
   * Return an array in tree form with nice bullets.
   *
   * @param array $items
   *   The array of items.
   *
   * @return string
   *   The string to output.
   */
  public static function tree(array $items) {
    $build = [];
    foreach ($items as $index => $item) {
      if ($index + 1 === count($items)) {
        $build[] = "└── $item";
      }
      else {
        $build[] = "├── $item";
      }
    }

    return implode(PHP_EOL, $build) . PHP_EOL;
  }

  /**
   * @param array $rows
   *   An associate array of rows, each element is a key value of cells.  Keys
   *   must match the keys in $header.  To control color for a row you need to
   *   pass the data in the 'data' key and pass the color argument in a key
   *   'color'.
   * @param array $header
   *   An associate array of key/values, each value is an array with the
   *   following:
   *   - align string One of left, center, right.  This determines the
   *   alignment for that column.
   * @param string $column_separator
   *   - a string to use for separating columns.
   *
   * @return string
   */
  public static function columns(array $rows, array $alignment = [], $column_separator = ' | ') {
    $build = [];

    $rows = array_map(function ($row) {
      return is_string($row) ? ['data' => $row] : $row;
    }, $rows);

    // Determine the column widths.
    $widths = [];
    foreach ($rows as $row_index => $cells) {
      $widths[$row_index] = array_map(function ($cell) {
        return Color::strlen($cell);
      }, $cells['data']);
    }
    $column_widths = [];
    foreach ($widths as $row_index => $cells) {
      foreach ($cells as $key => $width) {
        if (!isset($column_widths[$key])) {
          $column_widths[$key] = 0;
        }
        $column_widths[$key] = max($column_widths[$key], $width);
      }
    }

    foreach ($rows as $row_index => $cells) {
      $row = self::getColumnsRow($cells['data'], $column_separator, $column_widths, $alignment);
      if ($cells['color']) {
        $row = Color::wrap($cells['color'], $row);
      }
      $build[] = $row;
    }

    return implode(PHP_EOL, $build) . PHP_EOL;
  }

  /**
   * Return a single column row.
   *
   * @param $cells
   * @param $column_separator
   * @param $column_widths
   * @param $alignment
   *
   * @return string
   */
  private static function getColumnsRow($cells, $column_separator, $column_widths, $alignment) {
    $left = ltrim($column_separator);
    $right = rtrim($column_separator);
    foreach ($cells as $key => &$cell) {
      $align = !isset($alignment[$key]) ? 'right' : $alignment[$key];
      $cell_length = Color::strlen($cell);
      $pad = str_repeat(substr($column_separator, 0, 1), $column_widths[$key] - $cell_length);

      switch ($align) {
        case 'left':
          $cell .= $pad;
          break;

        case 'center':
          $center_pad = str_len(floor(strlen($pad) / 2));
          $cell = substr($pad, 0, $center_pad) . $cell . substr($pad, $center_pad);
          break;

        default:
          $cell = $pad . $cell;
          break;
      }
      continue;
    }

    return $left . implode($column_separator, $cells) . $right;
  }

}
