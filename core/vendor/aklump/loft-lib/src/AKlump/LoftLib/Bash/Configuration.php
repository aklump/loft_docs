<?php

namespace AKlump\LoftLib\Bash;

/**
 * A class to handle variables in BASH.
 */
class Configuration {

  protected $namespace;

  protected $separator;

  /**
   * Configuration constructor.
   *
   * @param string $namespace
   *   The plain text namespace for all variable names, it must begin with a
   *   letter, e.g. 'cloudy_config'.
   * @param string $separator
   *   The char(s) to use to join the config paths elements, e.g. '.', where
   *   the config path that is hashed will be 'foo.bar.baz'.
   */
  public function __construct($namespace, $separator = '.') {
    $this->namespace = $namespace;
    $this->separator = $separator;
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
    list($comment, $var_name) = $this->getVarName($this->namespace, $context['parents']);

    // Define the reason for stopping recursion and return.
    if (!is_array($value)) {
      $context['stack'][] = $this->getVarEvalCode($var_name, $value, $comment);

      return $context['stack'];
    }

    // Generate the keys.
    list(, $keys_var_name) = $this->getVarName($this->namespace . '_keys', $context['parents']);

    if (is_numeric(key($value))) {
      $context['stack'][] = $this->getVarEvalCode($var_name, array_values($value), $comment);
    }
    $context['stack'][] = $this->getVarEvalCode($keys_var_name, array_keys($value), '--keys ' . $comment);

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
   * @param string $prefix
   *   A plaintext string to to preceed the hash.
   *   e.g., "cloudy_config", "cloudy_config_keys".
   * @param array $variable_name_parts
   *   An array of strings that indicate the parent/child configuration path of
   *   nested arrays, ['foo', 'bar', 'baz'] where the path is 'foo.bar.baz'.
   *
   * @return string
   *   A hashed varible name that can be used by BASH.
   */
  private function getVarName($prefix, array $variable_name_parts) {
    if (!preg_match('/^[a-z]/i', $prefix)) {
      throw new \InvalidArgumentException("Prefix must begin with a letter.");
    }

    return [
      ($path = implode($this->separator, $variable_name_parts)),
      $path ? $prefix . '_' . md5($path) : $prefix,
    ];
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
   * @param string $comment
   *   A comment to proceed the eval code with.
   *
   * @return string
   *   Code to be used by BASH eval.
   */
  public function getVarEvalCode($var_name, $value, $comment = '') {
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

      $return = "declare -a $var_name=($open" . implode(' ', $value) . $close . ")";
    }
    else {

      $value = static::typecast($value);

      $return = $var_name . '=' . $this->quoteValue($value);
    }

    if ($comment) {
      $return = '# ' . trim($comment) . PHP_EOL . $return;
    }

    return $return;
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
