<?php

namespace AKlump\LoftLib\Code;

/**
 * This is a class to emulate the WordPress Shortcodes API.
 *
 * Shortcode names should be all lowercase and use all letters, but numbers
 * and underscores should work fine too. Be wary of using hyphens (dashes),
 * you'll be better off not using them.
 *
 * Don't use camelCase or UPPER-CASE for your $atts attribute names
 *
 * @link https://codex.wordpress.org/Shortcode_API
 * @link https://codex.wordpress.org/Shortcode_API#Handling_Attributes
 * @link https://codex.wordpress.org/Shortcode_API#Enclosing_vs_self-closing_shortcodes
 */
final class ShortCodes implements ShortCodesInterface {

  /**
   * The regex used to locate whitespace in shortcodes.
   *
   * @var string
   */
  const WS_REGEX = '(?:\s|&nbsp;)';

  /**
   * @var array
   */
  static $cache = [];

  /**
   * {@inheritdoc}
   */
  public static function prepare($base, array $element_value_map) {
    $callables = array_intersect_key($element_value_map, array_flip(array_map(function ($item) {
      return $item['name'];
    }, static::getElements($base))));
    foreach ($callables as $callable) {
      $base = $callable($base);
    }

    return $base;
  }

  /**
   * {@inheritdoc}
   */
  public static function inflate($base, array $element_value_map) {
    // These must be sorted longest first or replacement is messed up.
    uksort($element_value_map, function ($a, $b) {
      return strlen($b) - strlen($a);
    });
    $element_names = array_keys($element_value_map);
    array_walk($element_names, function ($element_name) {
      if (!preg_match('/^[a-z0-9_]/', $element_name)) {
        throw new \InvalidArgumentException('The element "' . $element_name . '" must be lowercase and only contain: letters, numbers and underscore.');
      }
    });
    $names_regex = array_map('preg_quote', $element_names);
    foreach ($names_regex as $name_regex) {
      $enclosing = sprintf('/\[%s*(%s)%s*([^\]]*)%s*\](.*?)\[\/(%s)\]/', self::WS_REGEX, $name_regex, self::WS_REGEX, self::WS_REGEX, $name_regex);
      $base = preg_replace_callback($enclosing, function ($matches) use ($base, $element_value_map) {
        return self::getElementReplacementValue($matches[1], $matches[2], $matches[3], $element_value_map);
      }, (string) $base);

      $self_closing = sprintf('/\[%s*(%s)%s*(.*?)%s*\]/', self::WS_REGEX, $name_regex, self::WS_REGEX, self::WS_REGEX);
      $base = preg_replace_callback($self_closing, function ($matches) use ($base, $element_value_map) {
        return self::getElementReplacementValue($matches[1], $matches[2], NULL, $element_value_map);
      }, (string) $base);
    }

    return $base;
  }

  /**
   * {@inheritdoc}
   */
  public static function getElements($base) {
    $cid = md5($base);
    if (!isset(static::$cache['elements'][$cid])) {

      // Generate a set of paired matchers based on opening tags.
      $self_closing = sprintf('\[%s*([a-z0-9_]*)%s*([^\]\/]*)%s*\]', self::WS_REGEX, self::WS_REGEX, self::WS_REGEX);
      preg_match_all("/($self_closing)/", $base, $matches, PREG_SET_ORDER);
      $matchers = array_map(function ($match) {
        $tag = $match[2];
        $self_closing = sprintf('\[%s*(%s)%s*([^\]\/]*)%s*\]', self::WS_REGEX, $tag, self::WS_REGEX, self::WS_REGEX);
        $enclosing = sprintf('\[%s*(%s)%s*([^\]\/]*)%s*\](.*?)\[\/%s*%s+\]', self::WS_REGEX, $tag, self::WS_REGEX, self::WS_REGEX, self::WS_REGEX, $tag);

        return "/($enclosing)|($self_closing)/";
      }, $matches);
      $elements = [];
      while ($regex = array_shift($matchers)) {
        preg_match_all($regex, $base, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
          $self_closing = !empty($match[5]);
          if ($self_closing) {
            $name = $match[6];
            $attributes = !empty($match[7]) ? $match[7] : '';
            $inner_html = '';
          }
          else {
            $name = $match[2];
            $attributes = !empty($match[3]) ? $match[3] : '';
            $inner_html = !empty($match[4]) ? $match[4] : '';
          }
          try {
            $attributes = self::parseAttributesString($attributes);
          }
          catch (\Exception $exception) {
            // Purposefully left blank, we'll silently ignore the offending
            // element and go on to the next one.
          }
          if ($name) {
            $elements[] = [
              'name' => $name,
              'inner_html' => strval($inner_html),
              'attributes' => $attributes,
            ];
          }
        }
      }
      static::$cache['elements'][$cid] = $elements;
    }

    return static::$cache['elements'][$cid];
  }

  /**
   * Convert a string of attributes to an array.
   *
   * @param string $string
   *   The attribute string, e.g., 'foo="bar" bar-baz="alpha"'.
   *
   * @return array
   *   The parsed key/value array.
   */
  private static function parseAttributesString($string) {
    $attributes = [];
    if (strstr($string, '=') && ($string = trim($string, '  '))) {
      $xml = @simplexml_load_string('<div ' . $string . '/>');
      if ($xml === FALSE) {
        throw new \RuntimeException("Malformed attributes: \"$string\"");
      }
      $attributes = current((array) $xml->attributes());
      $attributes = array_map(function ($value) {
        if (is_numeric($value)) {
          $value *= 1;
        }

        return $value;
      }, $attributes);
    }

    return $attributes;
  }

  /**
   * Handle the actual replacement of a given element.
   *
   * @param string $element_name
   *   The name of tag/element.
   * @param string $attribute_string
   *   A string of element attributes, e.g., 'do="re" mi="fa"'.
   * @param string $inner_html
   *   The inner HTML for non-self-closing tages.
   * @param array $element_value_map
   *   Keys are the element names, values are the element values or callbacks.
   *
   * @return string
   *   The calculated replacement value for the element.
   */
  private static function getElementReplacementValue($element_name, $attribute_string, $inner_html, array $element_value_map) {
    if (!isset($element_value_map[$element_name])) {
      throw new \InvalidArgumentException("Component \"$element_name\" is not available in the provided \$element_value_map array.");
    }
    $value = $element_value_map[$element_name];
    if (is_callable($value)) {
      $value = $value($inner_html, self::parseAttributesString($attribute_string));
    }

    if (is_object($value) && method_exists($value, '__toString')) {
      $value = strval($value);
    }
    if (!is_scalar($value)) {
      throw new \InvalidArgumentException("A element value must be a scalar; instead for " . $element_name . " received: " . gettype($value));
    }

    return $value;
  }

}
