<?php

namespace AKlump\LoftLib\Code;

final class CustomTags {

  /**
   * Replace React-style element names with rendered array values.
   *
   * @param string $base
   *   The string that contains elements to be replaced.
   * @param array $element_value_map
   *   An array keyed by element name, whose values are strings, callables, or
   *   objects that can be cast to string.  Be aware that element names must be
   *   Upper-camel, a.k.a. PascalCase.  Callables receive ($inner_html,
   *   $attributes).
   *
   * @code
   * <div>
   *   <h1><Title/></h1>
   *   <List/>
   *   <Link text="click"/>
   * </div>
   *
   * <?php
   * $element_value_map = [
   *   // String values are fine.
   *   'Title' => t('Static title'),
   *
   *   // Callables will receive an object of attributes pulled from the
   *   markup and inner html if it exists.
   *   'Link' => function ($inner_html, $attributes) {
   *     return t('<a href="!url"></a>', ['!url' => $attributes->text])];
   *   },
   * ];
   *
   * $html = CustomTags::replaceTags($base, $element_value_map);
   * @endcode
   *
   * @return string
   *   $base with all replacements made.
   *
   * @link https://github.com/airbnb/javascript/tree/master/react#naming
   * @link https://reactjs.org/docs/dom-elements.html
   */
  public static function replaceTags($base, array $element_value_map) {
    // These must be sorted longest first or replacement is messed up.
    uksort($element_value_map, function ($a, $b) {
      return strlen($b) - strlen($a);
    });
    $element_names = array_keys($element_value_map);
    array_walk($element_names, function ($element_name) {
      if (!preg_match('/^[A-Z]/', $element_name)) {
        throw new \InvalidArgumentException('The element "' . $element_name . '" must be UpperCamelCase.');
      }
    });
    $names_regex = array_map('preg_quote', $element_names);
    foreach ($names_regex as $name_regex) {
      $standard = '/<\s*(' . $name_regex . ')\s*([^>]*)\s*>(.*?)<\/(' . $name_regex . ')>/';
      $base = preg_replace_callback($standard, function ($matches) use ($base, $element_value_map) {
        return self::getElementReplacementValue($matches[1], $matches[2], $matches[3], $element_value_map);
      }, (string) $base);

      $self_closing = '/<\s*(' . $name_regex . ')\s*(.*?)\s*\/>/';
      $base = preg_replace_callback($self_closing, function ($matches) use ($base, $element_value_map) {
        return self::getElementReplacementValue($matches[1], $matches[2], NULL, $element_value_map);
      }, (string) $base);
    }

    return $base;
  }

  /**
   * Extract all React-style elements from a string.
   *
   * @param string $base
   *   The body of copy to search in.
   *
   * @return array
   *   If found, each array item points to an element and contains these keys:
   *   - name string The tagname
   *   - inner_html string The inner html if it exists.
   *   - attributes array An array of key/value attributes if exists.s
   */
  public static function getElements($base) {
    $self_closing = '<\s*([A-Z][^\s]*)\s*([^>\/]*)\s*\/>';
    $standard = '<\s*([A-Z][^\s]*)\s*([^>\/]*)\s*>(.*?)<\/\s*[A-Z][^\s]*\s*>';
    preg_match_all("/($standard)|($self_closing)/", $base, $matches, PREG_SET_ORDER);
    $elements = [];
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
      $elements[] = [
        'name' => $name,
        'inner_html' => strval($inner_html),
        'attributes' => self::parseAttributesString($attributes),
      ];
    }

    return $elements;
  }

  /**
   * Convert a string of attributes to an array.
   *
   * @param $string
   *   The attribute string, e.g., 'foo="bar" bar-baz="alpha"'.
   *
   * @return array
   *   The parsed key/value array.
   */
  private static function parseAttributesString($string) {
    $attributes = [];
    if (trim($string)) {
      $xml = simplexml_load_string('<div ' . $string . '/>');
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
