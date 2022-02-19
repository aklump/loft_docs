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
interface ShortCodesInterface {

  /**
   * Prepare/alter $base before inflating.
   *
   * Use this method to cleanup markup before calling ::inflate on $base.  For
   * example you may want to remove <p/> tags that are wrapping a shortcode
   * that returns a <p/> so that you don't have nexted <p/> tags.
   *
   * @param string $base
   *   The string that contains elements to be replaced.
   * @param array $element_value_map
   *   An array keyed by element name, whose values are callables that receive
   *   ($base) only if the shortcode element name exists in $base.  Each
   *   callable must return $base mutated as necessary.  The callback order is
   *   dependent on the order of keys in $element_value_map, not the order of
   *   the shortcodes as they appear in $base.
   */
  public static function prepare($base, array $element_value_map);

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
   * $html = ShortCodes::inflate($base, $element_value_map);
   * @endcode
   *
   * @return string
   *   $base with all replacements made.
   *
   * @link https://github.com/airbnb/javascript/tree/master/react#naming
   * @link https://reactjs.org/docs/dom-elements.html
   */
  public static function inflate($base, array $element_value_map);

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
  public static function getElements($base);
}
