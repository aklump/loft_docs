# Dataset

An PHP class to use for data objects, using JSON schema as a validation structure.

## Quick Start

1. Create a class that extends `Dataset`.
1. Now define the json schema.  A simple method is to supply a class constant `JSON_SCHEMA` with the schema value:

        class SimpleExample extends Dataset {
        
          const JSON_SCHEMA = '{"type": "object","required":["id"],"id":{"type":"integer"},"version":{"type":"string","default":"1.2.5"}}';
        
        }  
  
1. Most times however, your schema will live in a separate file.  Therefore you will not define the class constant `JSON_SCHEMA`, rather provide the path to the json schema as the return value of the public static method `pathToJsonSchema`.  You may follow the convention of appending `.schema.json` to the classname, if you wish, as shown here:

        /**
         * {@inheritdoc}
         */
        protected static function pathToJsonSchema() {
          return __DIR__ . '/DatasetAlpha.schema.json';
        }
 
1. Now create a [json schema file](https://json-schema.org/latest/json-schema-validation.html#rfc.section.10) to define your dataset at the path defined above.   
    1. Be aware that when you use the `pattern` keyword to define a regex expression that you do NOT include the delimiters like you do in PHP's `preg_match`.  This is corrent JSON: `"pattern": "\\?[^#]+"`, notice there are no delimiters.
3. Then implement an instance in your code like this:
    
        <?php
        $data = ['id' => 123];
        ...
        try {
            $timer = Timer::dataset($data)->validate()->throwFirstProblem();
        } catch (\Exception $exception) {
            // Do something if validation failed.
        }

### Using PHP class members in your JSON code with `static::`

You can provide class methods, constants, etc in your JSON schema files and they will be evaluated at runtime.  For example, here we provide the regex pattern for the `date` property with a class constant, and the `default` value with a class method.  The cornerstone of this process is that the value begin with `static::`.

        "date": {
            "type": "string",
            "default": "static::defaultDate()",
            "pattern": "static::REGEX_DATEISO8601"
        },
        
## Accessing Data

1. Get the complete dataset as an array (sorted, with defaults, etc): `$array = $data->get()`
2. Get the JSON value of the dataset by casting to a string: `$json = strval($data)`.
3. Use a property directly: `$id = $data->id`
4. Use a property's alias directly: `$id = $data->userId`.  Read about aliases for more info.

## Accessing Defaults

5. Get the default for single key: `Timer::getDefault('id')`.
6. Get an array of defaults: `Timer::getDefaults()`

## Detecting Errors

1. Return an array of all: `$data->getProblems`.
1. Throw an _\InvalidArgumentException_ with the first problem: `$data->throwFirstError`

## Setting Data

* You can alter a single key using `::mutate`.

## Aliases

You may have aliases for property keys, which means you can access the same value using any of a number of keys.  To define an alias use colon separation as seen below:

    {
        ...
        "properties": {
            "mi:me:moi": {
                "default": "myself",
                "type": "string",
                "pattern": "/^m.+/"
            },

## Custom Validation

1. If you have advanced validation beyond what comes for free, you may extend `validate()`, but read the docs there for what needs to happen.
2. Consider using `ignoreKey()` instead, if possible.

## How to Ignore a Key in Your Dataset (so as to not cause validation error)

    protected static function ignoreKey($key)
    {
        return $key === 'duration';
    }

    protected static function ignoreKey($key)
    {
        return strpos($key, '#') === 0;
    }

## Notes

* To ignore some keys use `static::ignoreKey()` in your class.

## Advanced Usage

### Auto-generate Values: Example 1

The time to do this is during `::import`. 

    <?php
    
    /**
     * Import extra data based on a default value.
     *
     * In this example, the defaults set the user id by global var.  During import
     * we check for a user_id, either by import $data or the default data.  Then we
     * make sure the the $original import data doesn't contain session_id, and if
     * so we pull that data from the user account object.
     */
    class Alpha extends Dataset {
    
      ... 
      
      /**
       * {@inheritdoc}
       */
      protected static function defaults() {
        global $user;
    
        return [
          'user_id' => $user->uid,
          'session_id' => -1,
        ];
      }
    
      /**
       * {@inheritdoc}
       */
      public function import($data) {
        $original = $data;
        $data += static::getDefaults();
    
        // Figure the session id based on the last time the user logged in.
        // https://amplitude.zendesk.com/hc/en-us/articles/115002323627-Sessions
        if ($data['user_id'] && empty($original['session_id'])) {
          $account = user_load($data['user_id']);
    
          // We will count our session based on last login.
          // https://drupal.stackexchange.com/questions/21864/whats-the-difference-between-user-login-and-access#21873
          $data['session_id'] = $account->login * 1000;
        }
    
        return parent::import($data);
      }
      
    }
