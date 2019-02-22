<?php
/**
 * @file
 * Defines the OutlineJson class.
 *
 * @ingroup name
 * @{
 */

namespace AKlump\LoftDocs;

use AKlump\Data\Data;
use AKlump\LoftLib\Storage\FilePath;

/**
 * Represents an OutlineJson object class.
 *
 * @brief Briefly describe what this class does.
 */
class OutlineJson implements IndexInterface {

  /**
   * Holds the json from the outline file.
   *
   * @var array
   */
  protected $json;

  /**
   * Constructor
   *
   * @param string $path_to_outline_file
   *   Outline json filepath.
   */
  public function __construct($path_to_outline_file) {
    $this->json = FilePath::create($path_to_outline_file, ['install' => FALSE])
      ->load()
      ->getJson(TRUE);
  }

  /**
   * Return a sorted chapter index.
   *
   * @return array
   *   Each item is a chapter with these keys.  The order is as the user has
   *   indicated they want it.
   *   - id
   *   - title
   */
  public function getChapterIndex() {
    $g = new Data();
    $info = $this->json + array(
        'chapters' => [],
      );
    $chapter_order = array_map(function ($item) {
      return $item['id'];
    }, $info['chapters']);
    foreach ($info['sections'] as $section) {
      $chapter_order[] = $g->get($section, 'chapter');
    }
    $chapter_order = array_unique(array_filter($chapter_order));
    // Add no chapters to the end.
    $chapter_order[] = '';

    $chapter_titles = [];
    foreach ($chapter_order as $id) {
      $chapter_titles[$id] = array_filter($info['chapters'], function ($item) use ($id) {
        return $item['id'] == $id;
      });
      $chapter_titles[$id] = empty($chapter_titles[$id]) ? [
        'id' => '',
        'title' => '',
      ] : reset($chapter_titles[$id]);
    }

    return $chapter_titles;
  }

  /**
   * Return an array of index data
   *
   * @return array
   *   Each element represents a page with these keys:
   *   - id
   *   - chapter array The chapter info.
   *   - file
   *   - title
   */
  public function getData() {
    $g = new Data();
    $info = $this->json + array(
        'sections' => array(),
      );
    $data = array();
    $index = array(
      'id' => 'index',
      'title' => 'Index',
      'file' => 'index.html',
    );

    foreach ($this->getChapterIndex() as $chapter_id => $chapter_data) {
      $chapter_sections = [];
      foreach ($info['sections'] as $value) {
        $this_chapter = $g->get($value, 'chapter', '');
        if ($this_chapter !== $chapter_id) {
          continue;
        }
        $key = pathinfo($value['file'], PATHINFO_FILENAME);
        if (in_array($key, array('index', 'advanced help settings'))
          && ($title = $this->getTitle($key, $value))
        ) {
          $index['title'] = $title;
        }
        else {
          $chapter_sections[$key] = array(
            'id' => $value['id'],
            'chapter' => $chapter_data['title'],
            'title' => $this->getTitle($key, $value),
            'file' => pathinfo($value['file'], PATHINFO_FILENAME) . '.html',
          );
        }
      }
      $data = array_merge($data, $chapter_sections);
    }

    // Sort and Flatten.
    $list = array('index' => $index) + $data;

    // Add in the prev and next links
    $prev = array();
    $last = NULL;
    foreach ($list as $key => $value) {
      $list[$key] += array(
        'prev_id' => 'index',
        'prev' => 'index.html',
        'prev_title' => 'Index',
        'next_id' => 'index',
        'next' => 'index.html',
        'next_title' => 'Index',
      );
      if ($last !== NULL) {
        $list[$last]['next_id'] = $value['id'];
        $list[$last]['next'] = $value['file'];
        $list[$last]['next_title'] = $value['title'];
      }
      if ($prev) {
        $list[$key] = $prev + $list[$key];
      }
      if ($value) {
        $prev = array(
          'prev_id' => $key,
          'prev' => $value['file'],
          'prev_title' => $value['title'],
        );
      }
      $last = $key;
    }

    // Set the index prev as the last in the list
    $last = end($list);
    $list['index']['prev_id'] = $last['id'];
    $list['index']['prev'] = $last['file'];
    $list['index']['prev_title'] = $last['title'];

    return $list;
  }

  public function getTitle($default, $value) {
    return isset($value['title']) ? $value['title'] :
      (isset($value['name']) ? $value['name'] : $default);
  }
}
