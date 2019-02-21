<?php

namespace AKlump\LoftLib\Code;

class DatasetBravo extends Dataset {

  /**
   * {@inheritdoc}
   */
  protected static function pathToJsonSchema() {
    return __DIR__ . '/DatasetBravo.schema.json';
  }

  protected static function ignoreKey($key) {
    return is_numeric($key);
  }
}

class SomeCustomClass {

}
