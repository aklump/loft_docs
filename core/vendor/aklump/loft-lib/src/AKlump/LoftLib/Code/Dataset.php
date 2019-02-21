<?php

namespace AKlump\LoftLib\Code;

use AKlump\Data\Data;

/**
 * Class Dataset
 *
 * Use this class when you want to define a specific schema for a dataset in
 * array form.  This class let's you define the variable types, default values,
 * required accept, and regex patterns for the values.
 *
 * Cast to string this object returns a json string.
 *
 * To ignore some keys use `static::ignoreKey()` in your class.  This is handy
 * if you're using this class to handle drupal build arrays, where you can only
 * validate on elements #....
 *
 * Normalizing data can be achieved on the way in via import, don't forget to
 * return $this.
 *
 * @code
 *   public function import($dataset) {
 *      parent::import($dataset);
 *      if (is_string($this->dataset['feature'])) {
 *          $this->dataset['feature'] = [$this->dataset['feature']];
 *      }
 *      return $this;
 *   }
 * @endcode
 *
 * @package AKlump\LoftLib\Code
 */
abstract class Dataset implements DatasetInterface {

  /**
   * Regex constant for validating ISO8601 dates.
   *
   * @var string
   */
  const REGEX_DATEISO8601 = '/^\d{4}\-\d{2}\-\d{2}(?:\T| )\d{2}\:\d{2}.*/';

  /**
   * Same as REGEX_DATEISO8601 but for use in JSON schema files.
   *
   * This removes the forward slash as they are not used in JSON schema regex.
   *
   * @var string
   */
  const JS_REGEX_DATEISO8601 = '^\d{4}\-\d{2}\-\d{2}(?:\T| )\d{2}\:\d{2}.*';

  protected static $json_schemas;

  protected static $schemas;

  /**
   * The JSON schema object.
   *
   * @var string
   */
  protected $jsonSchema;

  protected $withContext = FALSE;

  /**
   * The actual values imported into the instance.
   *
   * @var array
   */
  protected $dataset = [];

  /**
   * Holds validate problems.
   *
   * @var array
   */
  protected $problems = [];

  /**
   * AssetValidator constructor.
   *
   * You are advised NOT to change the constructor arguments.  You may extend
   * the constructor, but keep the argument the same and be sure to call
   * parent::__construct().
   *
   * @param array $dataset Optional.  Import dataset on instantiation.
   */
  public function __construct(array $dataset = []) {
    $this->import($dataset);
  }

  /**
   * Return the absolute path to the JSON schema file.
   *
   * @return string
   *   The absolute path to the json schema file which defines this class.
   */
  protected static function pathToJsonSchema() {
    return NULL;
  }

  /**
   * Load the schema into memory.
   *
   * By convention the schema is sought in the same directory as the php class
   * file, under the filename of the class replacing .php with .schema.json.  To
   * not follow this convention, you may override this method in your class.
   *
   * @return \AKlump\LoftLib\Code\DatasetInterface
   *   This instance.
   */
  protected static function jsonSchema() {
    $cid = get_called_class();
    if (empty(static::$json_schemas[$cid])) {
      if (defined('static::JSON_SCHEMA')) {
        $json = static::JSON_SCHEMA;
      }
      else {
        $path_to_schema = static::pathToJsonSchema();
        if (!file_exists($path_to_schema)) {
          throw new \InvalidArgumentException("Schema file does not exist: $path_to_schema");
        }
        if (!is_readable($path_to_schema)) {
          throw new \InvalidArgumentException("Cannot read schema file: $path_to_schema");
        }
        $json = file_get_contents($path_to_schema);
      }
      if (!(static::$json_schemas[$cid] = json_decode($json))) {
        throw new \InvalidArgumentException("Provided schema is invalid JSON.");
      }

      // Now we have to resolve the $refs.
      static::resolveJsonSchemaRefs(static::$json_schemas[$cid]);
    }

    return static::$json_schemas[$cid];
  }

  /**
   * Resolve all $ref elements in the schema defintion.
   *
   * @param mixed &$schema
   *   The json schema definition to start, after that recursion may send
   *   anything.
   * @param array $context
   *   Used internally for recursion tracking.
   *
   * @return object
   *   Since $schema is passed by reference, this is used only for recursion.
   */
  private static function resolveJsonSchemaRefs(&$schema, array &$context = []) {

    // Setup default(s).
    $context += ['schema' => $schema];
    if (is_scalar($schema) || is_null($schema)) {
      return $schema;
    }
    else {
      foreach ($schema as $k => $v) {
        if ($k === '$ref') {
          return static::getReferencedValue($v, $context['schema']);
        }
        elseif (is_array($schema)) {
          $schema[$k] = static::resolveJsonSchemaRefs($v, $context);
        }
        else {
          $schema->{$k} = static::resolveJsonSchemaRefs($v, $context);
        }
      }
    }

    return $schema;
  }

  /**
   * Get the actual value of a single reference.
   *
   * @param string $reference
   *   E.g. #/definitions/name.
   * @param \stdClass $schema
   *   The full JSON schema object.
   *
   * @return mixed
   *   The actual value.
   */
  private static function getReferencedValue($reference, \stdClass $schema) {
    list($file, $path) = explode('#/', $reference);
    if (!empty($file)) {
      $file = realpath(dirname(static::pathToJsonSchema()) . "/$file");
      if (!file_exists($file)) {
        throw new \RuntimeException("External referenced file: $file does not exist.");
      }
      $schema = json_decode(file_get_contents($file));
    }
    $path = explode('/', $path);
    while (count($path)) {
      $key = array_shift($path);
      if (!property_exists($schema, $key)) {
        throw new \RuntimeException("Cannot resolve $reference.");
      }
      $schema = $schema->{$key};
    }

    return $schema;
  }

  /**
   * Create a new instance from an array.
   *
   * @param array $dataset
   *   Optional. The started dataset array.
   *
   * @return \AKlump\LoftLib\Code\DatasetInterface
   *   A new instance with $dataset.
   */
  public static function create(array $dataset = []) {
    $class = get_called_class();

    return new $class($dataset);
  }

  /**
   * Alias of ::create.
   *
   * @param array $dataset
   *   Optional. The started dataset array.
   *
   * @return \AKlump\LoftLib\Code\DatasetInterface
   *   A new instance with $dataset.
   */
  public static function dataset(array $dataset = array()) {
    return static::create($dataset);
  }

  public static function example($version = 1) {
    $class = get_called_class();
    $ex = static::examples();
    if (!isset($ex[$version - 1])) {
      throw new \InvalidArgumentException("There is no example dataset with version $version.");
    }
    $data = $ex[$version - 1];

    return new $class($data);
  }

  public function import($data) {
    if (is_string($data)) {
      $data = json_decode($data, TRUE);
    }
    $this->dataset = (array) $data;

    return $this->validate();
  }

  /**
   * If you need to extend, you must never throw an error.  Instead you need to
   * add to $this->problems[$key][] with the problems encountered.
   *
   * Also you must return $this
   *
   * @return $this
   */
  public function validate() {
    $schema = $this->getSchema();
    $this->problems = [];

    // Review keys not found in the schema.
    $invalidKeys = array_keys(array_diff_key($this->dataset, $schema));
    if ($invalidKeys) {
      array_walk($invalidKeys, function ($key) {
        if (!static::ignoreKey($key)) {
          $this->problems[$key][] = "\"$key\" is not an accepted key";
        }
      }, $invalidKeys);
    }

    // Now review all schema keys.
    array_walk($schema, function ($s) {
      $id = $s['id'];

      // Required.
      if ($s['required'] && !array_intersect($s['aliases'], array_keys($this->dataset))) {
        $this->problems[$id][] = "Missing required field: $id";
      }

      if (isset($this->dataset[$id])) {
        $subject = $this->dataset[$id];

        // Check regex mask.
        if (!empty($s['mask']) && (!preg_match($s['mask'], $subject, $matches) || $matches[0] != $subject)) {
          $this->problems[$id][] = "Supplied value for \"$id\" of \"$subject\" does not match the regex format: " . $s['mask'];
        }

        // Check variable type.
        $type = gettype($subject);
        if (!in_array($type, $s['types'])) {
          $this->problems[$id][] = "Supplied value for \"$id\" of \"$subject\" ($type) is not one of type: " . implode(' or ', $s['types']);
        }
      }
    });

    return $this;
  }

  public function getProblems() {
    $context = $this->getVisibleContextForErrorHandler();

    return array_map(function ($problems) use ($context) {
      if (!$context) {
        return $problems;
      }

      return array_map(function ($problem) use ($context) {
        return $problem . $context;
      }, $problems);
    }, $this->problems);
  }

  public static function getSchema() {
    $cid = get_called_class();
    if (!isset(static::$schemas[$cid])) {
      $schema = [];

      $walkAliases = function ($key, &$schema, $callback) {
        $key = explode(':', $key);
        $aliases = array_values(array_filter(array_map(function ($value) use ($key) {
          return array_intersect($key, $value['aliases']) ? $value['aliases'] : NULL;
        }, $schema)));
        if ($aliases) {
          $aliases = $aliases[0];
          foreach ($aliases as $alias) {
            $callback($alias);
          }
        }
      };

      $accept = array_map(function ($item) {
        return explode(':', $item);
      }, static::acceptKeys());
      array_walk($accept, function ($aliases) use (&$schema) {
        foreach ($aliases as $alias) {
          $schema[$alias] = [
            'id' => $alias,
            'default' => NULL,
            'master' => ($master = reset($aliases)),
            'is_alias' => $master !== $alias,
            'aliases' => $aliases,
            'required' => FALSE,
            'mask' => NULL,
            'types' => ['string'],
            'description' => '',
          ];
        }
      });

      $required = static::requireKeys();
      array_walk($required, function ($key) use ($walkAliases, &$schema) {
        $walkAliases($key, $schema, function ($alias) use (&$schema) {
          $schema[$alias]['required'] = TRUE;
        });
      });

      $types = static::types();
      array_walk($types, function ($type, $key) use ($walkAliases, &$schema) {
        $walkAliases($key, $schema, function ($alias) use ($type, &$schema) {
          $schema[$alias]['types'] = explode('|', $type);
        });
      });

      $match = static::match();
      array_walk($match, function ($regex, $key) use ($walkAliases, &$schema) {
        $walkAliases($key, $schema, function ($alias) use ($regex, &$schema) {
          if (!in_array('string', $schema[$alias]['types'])) {
            throw new \RuntimeException("You may only declare a match value for string types; $alias is not a string.");
          }
          $schema[$alias]['mask'] = $regex;
        });

      });

      $defaults = static::defaults() + array_map(function ($item) {
          $type = reset($item['types']);

          return static::getTypeDefault($type);
        }, static::schemaRemoveAliases($schema));
      array_walk($defaults, function ($value, $key) use ($walkAliases, &$schema) {
        $walkAliases($key, $schema, function ($alias) use ($value, &$schema) {
          $schema[$alias]['default'] = $value;
        });

      });

      $descriptions = static::describe();
      array_walk($descriptions, function ($description, $key) use (&$schema) {
        $aliases = array_values(array_filter(array_map(function ($value) use ($key) {
          return in_array($key, $value['aliases']) ? $value['aliases'] : NULL;
        }, $schema)));
        if ($aliases) {
          foreach ($aliases[0] as $alias) {
            $schema[$alias]['description'] = $description;
          }
        }
      });
      static::$schemas[$cid] = $schema;
    }

    return static::$schemas[$cid];
  }

  public static function getDefaults() {
    return array_map(function ($item) {
      return $item['default'];
    }, static::schemaRemoveAliases(static::getSchema()));
  }

  public function throwFirstProblem() {
    if ($this->problems) {
      $p = reset($this->problems);
      $p = reset($p);
      $p .= $this->getVisibleContextForErrorHandler();
      throw new \InvalidArgumentException($p);
    }

    return $this;
  }

  public function getNoAlias() {
    $set = $this->get();
    $remove = [];
    foreach ($set as $key => $value) {
      $set = Arrays::replaceKey($set, $key, static::getMasterAlias($key));
      $remove = array_merge($remove, static::getNotMasterAliases($key));
    }

    $set = array_diff_key($set, array_flip(array_unique($remove)));

    return $set;
  }

  public function get() {
    $schema = static::getSchema();

    // Get an array with keys in correct order and defaults, but duplicated aliases...
    $set = array_map(function ($item) {
      return $this->__get($item['id']);
    }, $schema);

    foreach (array_keys($this->dataset) as $key) {

      if (!static::ignoreKey($key) && ($others = static::getOtherAliases($key))) {
        foreach ($others as $other) {
          unset($set[$other]);
        }
      }
    }

    $removeIfNotMaster = array_keys(array_diff_key($set, $this->dataset));
    foreach ($removeIfNotMaster as $key) {
      foreach (static::getNotMasterAliases($key) as $alias) {
        unset($set[$alias]);
      }
    }

    // This will add in the ignored keys.
    return $set + $this->dataset;
  }

  public static function getDefault($key) {
    $defaults = static::getDefaults();
    if (!array_key_exists($key, $defaults)) {

      // Search by alias
      foreach (static::getOtherAliases($key) as $alias) {
        if (array_key_exists($alias, $defaults)) {
          $key = $alias;
          // Return the first alias with a default value.
          break;
        }
      }
    }

    return $defaults[$key];
  }

  /**
   * Remove any keys that are aliases from a schema definition
   *
   * @param array $array
   *
   * @return array
   */
  public static function schemaRemoveAliases(array $array) {
    return array_filter($array, function ($item) {
      return !$item['is_alias'];
    });
  }

  protected static function ignoreKey($key) {
    return FALSE;
  }

  /**
   * Return the default value based on variable type.
   *
   * @param string $type As returned from gettype().
   *
   * @return array|\stdClass
   */
  protected static function getTypeDefault($type) {
    switch ($type) {

      case 'null':
        return NULL;

      case 'object':
        return new \stdClass();

      case 'array':
        return [];

      case 'boolean':
        return FALSE;

      case 'double':
        return doubleval(NULL);

      case 'integer':
        return 0;

      case 'string':
        return '';
    }

    return NULL;
  }

  /**
   * Define the accept allowed for the dataset.
   *
   * @return array
   * @codeCoverageIgnore
   */
  protected static function acceptKeys() {
    $keys = array_keys((array) static::jsonSchema()->properties);

    return $keys;
  }

  /**
   * Define example data.
   *
   * @return array
   *   An array of arrays, each is an example dataset.  You must return at
   *   least one example.  Default values will NOT be added in, you must
   *   include a complete recordset in each element.
   * @codeCoverageIgnore
   */
  protected static function examples() {
    return json_decode(json_encode(static::jsonSchema()->examples), TRUE);
  }

  /**
   * Define the required accept for the dataset.
   *
   * @return array
   *   An array of keys to require.
   *
   * @codeCoverageIgnore
   */
  protected static function requireKeys() {
    $schema = static::jsonSchema();

    return isset($schema->required) ? $schema->required : [];
  }

  /**
   * Return an array of master keys whose values are regex expressions which
   * each string value must completely match.
   *
   * You should only return keys that have been defined as string types.
   *
   * @return array
   *
   * @codeCoverageIgnore
   */
  protected static function match() {
    $match = [];
    foreach (static::jsonSchema()->properties as $name => $item) {
      if (isset($item->pattern)) {
        static::removeAliasKeysFromPropertyKeyDefinition($name);
        $match[$name] = '/' . static::runtimeEval($item->pattern) . '/';
      }
    }

    return $match;
  }

  /**
   * Process $value if it's a static:: call and return the computed value.
   *
   * @param mixed $value
   *   The value that may be processed if it begins with "static::".
   *
   * @return mixed
   *   The original or processed value, if applicable.
   */
  private static function runtimeEval($value) {
    if (!is_string($value) || strpos($value, 'static::') !== 0) {
      return $value;
    }

    $value = preg_replace_callback("/static::([^\s\"]+)/", function ($eval) {
      $eval = str_replace('static', static::class, $eval[0]);
      $eval = 'return ' . rtrim($eval, ';') . ';';

      return eval($eval);
    }, $value);

    return $value;
  }

  /**
   * Define non-null default values for accept.
   *
   * @return array
   *   Keys are master accept, values are default values other than null.
   * @codeCoverageIgnore
   */
  protected static function defaults() {
    $defaults = [];
    foreach (static::jsonSchema()->properties as $name => $item) {
      static::removeAliasKeysFromPropertyKeyDefinition($name);
      if (isset($item->default)) {
        $defaults[$name] = static::runtimeEval($item->default);
      }
      else {
        $type = $item->type;
        $type = is_array($type) ? reset($type) : $type;
        $defaults[$name] = static::getTypeDefault($type);
      }
    }

    return $defaults;
  }

  /**
   * Remove any aliases defined in a key.
   *
   * A key defined as mi:me:moi will be reduced to mi.
   *
   * @param string &$name
   *   The property key to be processed.
   */
  private static function removeAliasKeysFromPropertyKeyDefinition(&$name) {
    $name = explode(':', $name);
    $name = reset($name);
  }

  /**
   * Define non-string datatypes for accept.
   *
   * @return array
   *   Keys are master accept, values are the data type of the value.  Multiple
   *   types are separated by |.  Default type is string and does not need to
   *   be listed.
   * @codeCoverageIgnore
   */
  protected static function types() {
    return array_map(function ($item) {
      if (!isset($item->type)) {
        $type = 'string';
      }
      else {
        $type = is_array($item->type) ? implode('|', $item->type) : $item->type;
      }

      return $type;
    }, (array) static::jsonSchema()->properties);
  }

  /**
   * Describe each key in sentence form.
   *
   * @return array
   *  Keys are master accept, values are the definitions.
   * @codeCoverageIgnore
   */
  protected static function describe() {
    $descriptions = [];
    foreach (static::jsonSchema()->properties as $name => $item) {
      if (isset($item->description)) {
        static::removeAliasKeysFromPropertyKeyDefinition($name);
        $descriptions[$name] = $item->description;
      }
    }

    return $descriptions;
  }

  protected static function getNotMasterAliases($alias) {
    return static::getOtherAliases(static::getMasterAlias($alias));
  }

  protected static function getMasterAlias($alias) {
    $schema = static::getSchema();
    $aliases = $schema[$alias]['aliases'];

    return array_reduce($aliases, function ($carry, $item) use ($schema) {
      return $carry . ($schema[$item]['is_alias'] ? '' : $item);
    }, '');
  }

  protected static function getAllAliases($alias) {
    $schema = static::getSchema();
    if (!array_key_exists($alias, $schema)) {
      throw new \InvalidArgumentException("\"$alias\" is not a valid schema key.");
    }

    return array_values($schema[$alias]['aliases']);
  }

  protected static function getOtherAliases($alias) {
    return array_values(array_filter(static::getAllAliases($alias), function ($item) use ($alias) {
      return $item !== $alias;
    }));
  }

  /**
   * Mutate the key and return a new object.
   *
   * @param string $key
   *   The key whose value will change.
   * @param mixed $value
   *   The new value.
   *
   * @return \AKlump\LoftLib\Code\Dataset
   *   The object with mutated data.
   */
  public function mutate($key, $value) {
    $object_data = $this->get();
    $object_data[$key] = $value;
    $classname = get_class($this);

    return $classname::dataset($object_data);
  }

  public function __toString() {
    return json_encode($this->get());
  }

  public function getMarkdown() {
    $schema = static::getSchema();
    $accept = array_keys($schema);
    sort($accept);

    return Markdown::table(array_map(function ($key) use ($schema) {
      $s = $schema[$key];
      $build = array();

      if ($s['is_alias']) {
        $build['key'] = "$key*";
        $build['types'] = '';
        $build['required'] = '';
        $build['example'] = '';
        $build['description'] = 'Alias of `' . $s['master'] . '`';
      }
      else {
        $build['key'] = "$key";
        $build['types'] = implode(', ', $s['types']);
        $build['required'] = $s['required'] ? 'yes' : 'no';
        $build['example'] = $this->__get($key);
        $build['description'] = $s['description'];
      };

      return $build;
    }, $accept));
  }

  /**
   * Access the value by alias or actual key.
   *
   * Add any extra keys for dynamic content as needed.
   *
   * @param            $key
   *
   * @return mixed
   */
  public function __get($key) {
    $data = $this->dataset;
    $g = new Data();
    $default = static::getDefault($key);

    return $g->get($data, $key, $default, function ($value, $default, $exists) use ($data, $key) {
      if (!$exists) {
        $aliases = static::getOtherAliases($key);
        foreach ($aliases as $alias) {
          if (array_key_exists($alias, $data)) {
            return $data[$alias];
          }
        }
      }

      return $value;
    });
  }

  /**
   * Set it up so next method call will return the data context.
   *
   * @return $this
   * @see throwFirstProblem().
   * @see getProblems().
   */
  public function withContext() {
    $this->withContext = TRUE;

    return $this;
  }

  protected function getVisibleContextForErrorHandler() {
    $context = $this->withContext ? ' in ' . static::class . ' having ' . json_encode($this->dataset) : '';
    $this->withContext = FALSE;

    return $context;
  }
}
