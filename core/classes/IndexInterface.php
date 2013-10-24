<?php
/**
 * @file
 * Defines the AdvancedHelp class
 *
 * @ingroup loft_docs
 * @{
 */

namespace aklump\loft_docs;

/**
 * Interface IndexInterface
 */
interface IndexInterface {

  /**
   * Get the index data from the source file and return as array in proper order
   *
   * @return array
   *   Keys are the page ids
   *   Values are arrays with the following keys
   *   - id string The page id.
   *   - title string The title of the page.
   *   - file string The basename of the file.
   *   - prev string The page id of the previous page.
   *   - prev_title The page title of the previous page.
   *   - next string The page id of the next page.
   *   - next_title string The page title of the next page.
   */
  public function getData();

  /**
   * Return the correct title for a page
   *
   * @param string $default
   *   The should probably be the page id.
   * @param array $value
   *   Looking for keys: title or name
   *
   * @return string
   */
  public function getTitle($default, $value);
}

/**
 * Class Index
 */
class AdvancedHelpIni implements IndexInterface {

  protected $path;

  /**
   * Constructor
   * @param path $path
   *   The path to the ini file
   */
  public function __construct($path) {
    $this->path = $path;
  }

  public function getTitle($default, $value) {
    return isset($value['title']) ? $value['title'] :
      (isset($value['name']) ? $value['name'] : $default);
  }

  public function getData() {
    $info = parse_ini_file($this->path, TRUE);
    $data = array();

    foreach ($info as $key => $value) {
       $weight = isset($value['weight']) ? $value['weight'] : 0;
      if ($key === 'advanced help settings') {
        $index = array(
          'id' => 'index',
          'title' => $this->getTitle($key, $value),
          'file' => 'index.html',
        );
      }
      else {
        $data[$weight][$key] = array(
          'id' => $key,
          'title' => $this->getTitle($key, $value),
          'file' => $key . '.html',
        );
      }
    }

    //Sort and Flatten
    ksort($data);
    $list = array();
    foreach ($data as $value) {
      foreach ($value as $key => $value2) {
        $list[$key] = $value2;
      }
    }
    $list = array('index' => $index) + $list;

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
        $list[$last]['next_id'] = $key;
        $list[$last]['next'] = $value['file'];
        $list[$last]['next_title'] = $value['title'];
      }
      if ($prev) {
        $list[$key] = $prev + $list[$key];
      }
      $prev = array(
        'prev_id' => $key,
        'prev' => $value['file'],
        'prev_title' => $value['title'],
      );
      $last = $key;
    }

    // Set the index prev as the last in the list
    $last = end($list);
    $list['index']['prev_id'] = $last['id'];
    $list['index']['prev'] = $last['file'];
    $list['index']['prev_title'] = $last['title'];
    return $list;
  }
}

/** @} */ //end of group: loft_docs
