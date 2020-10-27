<?php

namespace AKlump\LoftLib\Bash;

/**
 * A class to handle variables in BASH.
 *
 * @deprecated Will be removed in 2.0.  Use \AKlump\LoftLib\Bash\Configuration
 *   instead.
 */
class LegacyConfiguration {

  const VAR_NAME_PREFIX = '';

  const SEPARATOR = '___';

  public function __construct($var_name_prefix = NULL, $separator = NULL) {
    $this->varNamePrefix = static::VAR_NAME_PREFIX;
    $this->separator = $this->varNamePrefix;
    if (!is_null($var_name_prefix)) {
      $this->varNamePrefix = $var_name_prefix;
    }
    if (!is_null($separator)) {
      $this->separator = $separator;
    }
  }

  /**
   * Convert a multidimensional data array into BASH declarations.
   *
   * @param mixed $value
   *   The starting data array or final value.
   * @param array $context
   *   Has the following keys:
   *   - stack array
   *     This can be seeded with elements returned in the final array.
   *
   * @return array
   *   A single dimension array of BASH declarations representing the variables.
   */
  public function flatten($value, array &$context = []) {

    // Setup defaults.
    $context += ['parents' => [], 'stack' => []];
    $var_name = $this->getVarName($context['parents'], $this->varNamePrefix);

    // Define the reason for stopping recursion and return.
    if (!is_array($value)) {
      $context['stack'][] = $this->getVarEvalCode($var_name, $value);

      return $context['stack'];
    }

    // Generate the keys.
    $keys_var_name = $this->getVarName($context['parents'], $this->varNamePrefix . '_keys');

    if (is_numeric(key($value))) {
      $context['stack'][] = $this->getVarEvalCode($var_name, array_values($value));
    }
    $context['stack'][] = $this->getVarEvalCode($keys_var_name, array_keys($value));

    // Otherwise recurse.
    foreach ($value as $k => $v) {
      $context['parents'][] = $k;
      $this->flatten($v, $context);
      array_pop($context['parents']);
    }

    return $context['stack'];
  }

  /**
   * Generate a BASH variable name.
   *
   * @param array $parents
   *   Usually this is $context['parents'].
   * @param string ...
   *   Additional elements to merge into the key.
   *
   * @return string
   *   A stack key variable name.
   */
  private function getVarName(array $parents, $prefix = '') {
    $args = func_get_args();
    $components = array_shift($args);
    $prefix = array_shift($args);
    if ($prefix) {
      array_unshift($components, $prefix);
    }
    if (count($args)) {
      $components = array_merge($components, $args);
    }

    return ltrim(implode('___', $components));
  }

  /**
   * Ensure proper quotes around a variable value.
   *
   * @param mixed $value
   *   The variable value.
   *
   * @return mixed
   *   The quoted variable value.
   */
  private function quoteValue($value, $force = FALSE) {
    $value = str_replace('"', '\"', $value);

    if (!$force && is_numeric($value) && strlen($value) === strlen($value * 1)) {
      $value = $value * 1;
    }
    elseif (!$force && in_array($value, ['true', 'false', 'null'])) {
      $value = $value;
    }
    elseif (is_array($value)) {
      // TODO https://trello.com/c/RK8pPYjl/44-c-b-732-certain-types-of-arrays-are-not-supported-in-yml-config-yet
      $value = '';
    }
    else {
      $value = '"' . $value . '"';
    }

    return $value;
  }

  /**
   * Create a BASH eval declaration for a variable and value.
   *
   * @param string $var_name
   *   The bash variable name to use.
   * @param mixed $value
   *   The value of the variable.
   *
   * @return string
   *   Code to be used by BASH eval.
   */
  public function getVarEvalCode($var_name, $value) {
    $var_name = str_replace('-', '_', $var_name);
    if (is_array($value)) {
      array_walk($value, function (&$value) {
        $value = static::typecast($value);
        // Array values appear to need quotes always.
        $value = $this->quoteValue($value, TRUE);
      });

      if (empty($value)) {
        return "declare -a $var_name=()";
      }

      $open = substr($value[0], 0, 1) === '"' ? '' : '"';
      $close = substr($value[count($value) - 1], -1) === '"' ? '' : '"';

      return "declare -a $var_name=($open" . implode(' ', $value) . $close . ")";
    }

    $value = static::typecast($value);

    return $var_name . '=' . $this->quoteValue($value);
  }

  /**
   * Cast a PHP value to the appropriate string value in bash.
   *
   * @param mixed $value
   *   The value to convert to a BASH string.
   *
   * @return int|string
   */
  public static function typecast($value) {
    if ($value === NULL) {
      $value = 'null';
    }
    elseif ($value === TRUE) {
      $value = 'true';
    }
    elseif ($value === FALSE) {
      $value = 'false';
    }

    return $value;
  }

}
