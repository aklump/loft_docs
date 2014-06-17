<?php
/**
 * @file
 * Boostrapper and function declarations
 *
 * @ingroup loft_docs
 * @{
 */

/**
 * Parse all todos in a string
 *
 * @param  string $string Looking for this pattern  '- [ ] @todo...EOL'
 * @param  string $prefix A prefix to the todo item; used to include the file
 * name in the todo item.
 *
 * @return array         
 */
function parse_todos($string, $prefix = '') {
  $todos = array();
  if (is_string($string)
    //&& preg_match_all('/- \[ \] @todo.*$/m', $string, $matches)) {
    && preg_match_all('/- \[ \] .*$/m', $string, $matches)) {
    
    if (!empty($prefix) || !empty($remove_todo)) {
      foreach (array_keys($matches[0]) as $key) {

        if ($prefix) {
          $matches[0][$key] = str_replace('- [ ] ', "- [ ] $prefix", $matches[0][$key]);
        }
      }
    }    
    $todos = $matches[0];
  }

  return $todos;
}

/**
 * Flattens an array of todo items into a string
 *
 * @param  array $array
 *
 * @return string        
 */
function flatten_todos($array) {
  if (is_array($array)) {
    return implode("\n", array_filter($array)) . "\n";
  }
}

/**
 * Sort an array of todo items by @w flag
 *
 * @param  array &$todos
 */
function sort_todos(&$todos) {
  if (is_array($todos)) {
    usort($todos, '_sort_todos');
    $todos = array_values(array_filter(array_unique($todos)));
  }
}

/**
 * Helper for usort
 */
function _sort_todos($a, $b) {
  if (get_weight($a) === get_weight($b)) {
    return 0;
  }

  return get_weight($a) > get_weight($b) ? 1 : -1;
}

/**
 * Return the numeric weight of a todo item
 *
 * @param  string $string
 *
 * @return int||float
 */
function get_weight($string) {
  if (preg_match_all('/@w([\d\.]+)/', $string, $matches)) {
    return 1 * end($matches[1]);
  }

  return 0;
}