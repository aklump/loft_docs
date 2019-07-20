<?php

namespace AKlump\LoftLib\Code;

/**
 * A class for working with arrays.
 */
class Arrays {

  /**
   * @param      $formArray A multi-dimensional array.
   * @param      $name      The key by which to fuzzy match.
   * @param null $default A Default value.
   *
   * @return mixed
   */
  public static function formFuzzyGet($formArray, $name, $default = NULL) {
    $a = Arrays::formExpand($formArray);
    $b = Arrays::formExpand(array($name => TRUE));

    return static::_formFuzzyGet($a, $b, $default);
  }

  /**
   * Convert a formExport array back to it's multidimensional representation.
   *
   * @param array $array
   * @param int $expansionKey
   *
   * @return array
   *
   * The expansion key instructs how to handle the following merge.  The
   * $expansionKey takes the place of the missing final key on the first
   * value.
   * @code
   *   _val[comp]          => dir/file.png,
   *   _val[comp][0][type] => mobile,
   * @endcode
   *
   * So the effect is the following:
   * @code
   *   _val[comp][0][$expansionKey] => dir/file.png,
   *   _val[comp][0][type]          => mobile,
   * @endcode
   */
  public static function formExpand(array $array, $expansionKey = 0) {
    // By putting these in reverse order, it builds the array correctly, as you might be expecting.
    ksort($array);
    $out = array();
    foreach ($array as $name => $value) {
      $parents = array($name);
      if (preg_match('/(.+?)\[(.+)\]/', $name, $matches)) {
        $parents = explode('][', $matches[2]);
        array_unshift($parents, $matches[1]);
      }
      static::_formExpandItem($out, $parents, $value, $expansionKey);
    }

    return $out;
  }

  /**
   * Return a new array with all keys from $a, whose keys begin with any of
   * the keys in $b.
   *
   * @param array $a
   * @param array $b
   *
   * @return array
   */
  public static function formFuzzyIntersectKey(array $a, array $b) {
    $intersection = array();
    foreach (array_keys($b) as $fuzzy) {
      foreach (array_keys($a) as $key) {
        if (strpos($key, $fuzzy) === 0) {
          $intersection[$key] = $a[$key];
        }
      }
    }

    return $intersection;
  }

  /**
   * Shuffle an array maintaining key association.
   *
   * @param array $array
   *   The array to shuffle.
   *
   * @return array
   *   The new array with shuffled order and preserved keys.
   */
  public static function shuffleWithKeys(array $array) {
    $keys = array_keys($array);
    shuffle($keys);
    do {
      $shuffled = array_map(function ($key) use ($array) {
        return $array[$key];
      }, array_combine($keys, $keys));
    } while ($shuffled === $array);

    return $shuffled;
  }

  /**
   * Return a new array with a key renamed, maintain the element value.
   *
   * @param array $array
   *   The original array.
   * @param string $old_key
   *   The key to replace.
   * @param string $new_key
   *   The new key to replace with.
   *
   * @return array
   *   A new array with the key replaced.
   */
  public static function replaceKey(array $array, $old_key, $new_key) {
    $keys = array_keys($array);
    $index = array_search($old_key, $keys);

    if ($index !== FALSE) {
      $keys[$index] = $new_key;
      $array = array_combine($keys, $array);
    }

    return $array;
  }

  /**
   * Replace all $oldValue with $newValue in an array.
   *
   * @param array $array
   * @param $oldValue
   * @param $newValue
   *
   * @return array
   */
  public static function replaceValue(array $array, $oldValue, $newValue) {
    $keys = array_keys($array, $oldValue, TRUE);
    foreach ($keys as $key) {
      $array[$key] = $newValue;
    }

    return $array;
  }

  /**
   * Insert an array or string in an array before a given value returning the
   * resulting first key.
   *
   * @param array $array
   * @param       $search
   * @param mixed $insert
   *
   * @return int|string The index of the start of the $insert.
   */
  public static function insertBeforeValue(array &$array, $search, $insert) {
    if (($key = array_search($search, $array)) !== FALSE) {
      if (!is_numeric($key)) {
        throw new \InvalidArgumentException("Only indexed arrays are supported.");
      }
      $position = $key;
      array_splice($array, $position, 0, $insert);

      return $position;
    }
    $array[] = $insert;

    return count($array) - 1;
  }

  /**
   * Insert an array or string in an array after a given value returning the
   * resulting first key.
   *
   * @param array $array
   * @param       $search
   * @param mixed $insert
   *
   * @return int|string The index of the start of the $insert.
   */
  public static function insertAfterValue(array &$array, $search, $insert) {
    if (($key = array_search($search, $array)) !== FALSE) {
      if (!is_numeric($key)) {
        throw new \InvalidArgumentException("Only indexed arrays are supported.");
      }
      $position = $key + 1;
      array_splice($array, $position, 0, $insert);

      return $position;
    }
    $position = count($array);
    $array[] = $insert;

    return $position;
  }

  /**
   * Insert an associative array right before a key.
   *
   * @param array $array
   * @param string $search The key to insert before.
   * @param array $insert The array to insert.
   */
  public static function insertBeforeKey(array &$array, $search, array $insert) {
    if (is_numeric($search)) {
      throw new \InvalidArgumentException("\$search may not be numeric.");
    }
    $a = [];
    $b = $array;
    if (($offset = array_search($search, array_keys($array)))) {
      $a = array_slice($array, 0, $offset);
      $b = array_slice($array, $offset);
    }
    $array = array_merge($a, $insert, $b);
  }

  /**
   * Insert an associative array right after a key.
   *
   * @param array $array
   * @param string $search The key to insert after.
   * @param array $insert The array to insert.
   */
  public static function insertAfterKey(array &$array, $search, array $insert) {
    if (is_numeric($search)) {
      throw new \InvalidArgumentException("\$search may not be numeric.");
    }
    $offset = array_search($search, array_keys($array));
    $a = array_slice($array, 0, $offset + 1);
    $b = array_slice($array, $offset + 1);
    $array = array_merge($a, $insert, $b);
  }

  /**
   * Compares they keys of $a against $b and returns the values in $a that
   * are not present in $b based on a fuzzy key match (The keys of $a do not
   * begin with the keys of $b).
   *
   * @param array $a
   * @param array $b
   *
   * @return array
   */
  public static function formFuzzyDiffKey(array $a, array $b) {
    foreach (array_keys($b) as $fuzzy) {
      foreach (array_keys($a) as $key) {
        if (strpos($key, $fuzzy) === 0) {
          unset($a[$key]);
        }
      }
    }

    return $a;
  }

  /**
   * Reduce a multidimensional array to single to use as http form values.
   *
   * @param array $array
   * @param string|array $prefix A parent tree string or array.
   *
   * @return array
   */
  public static function formExport(array $array, $parents = array()) {
    $null = array();
    $parents = empty($parents) ? array() : $parents;
    $parents = is_string($parents) ? static::expandParents($parents) : $parents;

    return static::_formExport($array, $null, $parents);
  }

  protected static function _formFuzzyGet($a, $b, $default) {
    if (!is_array($b)) {
      return $a;
    }
    else {
      foreach (array_keys($b) as $key) {
        if (!array_key_exists($key, $a)) {
          return $default;
        }

        if ($return = static::_formFuzzyGet($a[$key], $b[$key], $default)) {
          break;
        }
      }
    }

    return $return;
  }

  protected static function _formExpandItem(array &$build, array $parents, $value, $expansionKey) {
    $parent = array_shift($parents);
    if (count($parents)) {
      if (isset($build[$parent])) {
        $build[$parent] = is_array($build[$parent]) ? $build[$parent] : array(0 => array($expansionKey => $build[$parent]));
      }
      else {
        $build[$parent] = array();
      }
      static::_formExpandItem($build[$parent], $parents, $value, $expansionKey);
    }
    else {
      $build[$parent] = $value;
    }
  }

  /**
   * Recursive helper method.
   *
   * @see formExport().
   */
  protected static function _formExport($value, array &$export = array(), array &$parents = array()) {
    if (is_array($value) && !empty($value)) {
      foreach ($value as $key => $item) {
        $parents[] = $key;
        static::_formExport($item, $export, $parents);
      }
      array_pop($parents);
    }
    elseif ($parents) {
      $export[static::flattenParents($parents)] = $value;
      array_pop($parents);
    }

    return $export;
  }

  /**
   * Given a single dimensional array, return the values as a string
   * representing the parent structure of a form item.
   *
   * @param array $array E.g., ['do', 're', 'mi']
   *
   * @return mixed|string E.g. 'do[re][mi]'
   *
   * @see expandParents().
   */
  public static function flattenParents(array $array) {
    $tree = array_shift($array);
    if ($array) {
      $tree .= '[' . implode('][', $array) . ']';
    }

    return strval($tree);
  }

  /**
   * The opposite of flattenParents.
   *
   * @param $string , e.g. 'do[re][mi]'
   *
   * @return array
   *
   * @see flattenParents().
   */
  public static function expandParents($string) {
    $a = trim($string);
    $a = $a ? explode('[', $string) : array();
    array_walk($a, function (&$value) {
      $value = trim($value, '[]');
    });

    return $a;
  }

  /**
   * Implode an array with an optional final glue that is different.[
   *
   * This method facilitates English style lists where you have something like
   * "do, re and mi".
   *
   * @param string $glue
   *   The glue between $pieces.
   * @param string $final_glue
   *   The glue for attaching the last element, e.g. ' and ', ' or '.
   * @param array $pieces
   *   An indexed array of "words".
   *
   * @return string
   */
  public static function listImplode($glue, $final_glue, array $pieces) {
    if ($final_glue && count($pieces) > 1) {
      $last = array_pop($pieces);

      return implode($glue, $pieces) . $final_glue . $last;
    }

    return implode($glue, $pieces);
  }

}
