<?php

namespace AKlump\LoftLib\Code;

/**
 * A class to use for unit testing.
 */
class DatasetAlpha extends Dataset {

  /**
   * {@inheritdoc}
   */
  protected static function pathToJsonSchema() {
    return __DIR__ . '/DatasetAlpha.schema.json';
  }

  /**
   * Provide a default date value.
   *
   * @return string
   *   A UTC ISO 8601 date for the current moment in time.
   */
  protected static function defaultDate() {
    return Dates::z()->format(DATE_ISO8601);
  }

}
