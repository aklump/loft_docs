<?php

namespace AKlump\LoftLib\Storage;

use AKlump\LoftLib\Code\LoftXmlElement;
use AKlump\LoftLib\Code\ObjectCacheTrait;
use AKlump\LoftLib\Code\ThrowableErrorsTrait;

/**
 * Class FilePath
 *
 * A wrapper class for writing to directories or files.  Can also read, but do
 * not use this class unless you intend to write, since instantiation will
 * create the parent directories.
 *
 * In this first example we are using the object as a "directory" object.
 *
 * @code
 *   $dir = FilePath::create('/to/my/files');
 *
 *   //
 *   //
 *   // Create a new json file in the directory.
 *   //
 *   $file = $dir->put('{"json":true}')
 *         ->to('test.json')
 *         ->save();
 *
 *   // ... or you can put an array as a JSON string like this:
 *   $file = $dir->putJson(['json' => true])
 *         ->to('test.json')
 *         ->save();
 *
 *   // You can then get the contents...
 *   $json = $file->get();
 *   $json === '{"json":true}';
 *   true === file_exists('/to/my/files/test.json');
 *
 *   // .. or get then as an object, since we know they are json encoded.
 *   $data = $file->getJson();
 *   $data == (object) ['json' => true];
 *
 *   //
 *   //
 *   // Load another file called records.txt from the directory
 *   //
 *   $contents = $dir->from('records.txt')
 *                     ->load()
 *                     ->get();
 *
 *   //
 *   //
 *   // Load a json file to a data array
 *   //
 *   $data = $dir->from('records.txt')->loadJson()->get();
 * @endcode
 *
 * In this next example we are using the object as as "file" object so the
 * syntax is simpler since we have indicated the $id in the constructor, by
 * passing a path to the filename, not the directory.
 *
 * @code
 *   $file = FilePath::create('/to/my/file.json');
 *
 *   //
 *   //
 *   // Save some json to the file.
 *   //
 *   $file->putJson(['title' => 'Book'])->save();
 *
 *   //
 *   //
 *   // ...later on load the json from the file;
 *   //
 *   $data = $file->load()->getJson();
 * @endcode
 *
 * @package AKlump\LoftLib\Component\Storage
 */
class FilePath implements PersistentInterface {

  use ObjectCacheTrait;
  use ThrowableErrorsTrait;

  const TYPE_DIR = 1;

  const TYPE_FILE = 2;

  protected $dir, $basename, $contents, $type, $alias;

  private $config = [];

  /**
   * FilePath constructor.
   *
   * @see ::create
   */
  public function __construct($path, $options = []) {
    $options += [
      'parents' => isset($options['install']) ? $options['install'] : TRUE,
      'autoname' => isset($options['extension']) ? $options['extension'] : NULL,
    ];

    // Generate the temporary name.
    if (!empty($options['autoname'])) {
      if (pathinfo($path, PATHINFO_EXTENSION)) {
        throw new \InvalidArgumentException("When providing an extension, \$path must reference a directory.");
      }

      // Make sure the extension is not a filename or a path.
      $test = explode('.', trim($options['autoname'], '.'));
      if (count($test) > 1 || strpos($options['autoname'], '/') !== FALSE) {
        throw new \InvalidArgumentException("\$extension appears to be a filename; it must only contain the extension, e.g. 'pdf', and no leading dot");
      }
      $path = rtrim($path, '/') . '/' . static::tempName($options['autoname']);
    }

    // Detect the type from an existing file.
    if (file_exists($path)) {
      $_type = empty($options['autoname']) && is_dir($path) ? self::TYPE_DIR : self::TYPE_FILE;
    }

    // Set the type based on $options or guessing.
    else {
      $_ext = $options['autoname'] ? $options['autoname'] : pathinfo($path, PATHINFO_EXTENSION);
      if (array_key_exists('type', $options)) {
        $_type = $options['type'];
      }
      else {
        // Legacy support.
        $_is_dir = array_key_exists('is_dir', $options) ? $options['is_dir'] : NULL;
        if (is_null($_is_dir)) {
          $_is_dir = empty($_ext);
        }
        $_type = $_is_dir ? self::TYPE_DIR : self::TYPE_FILE;
      }
    }

    $options += [
      'type' => $_type,
    ];

    $this->type = $_type;
    $path_is_dir = $_type === self::TYPE_DIR;
    $path = trim($path);
    $this->config = $options;

    $this->dir = rtrim($path_is_dir ? $path : dirname($path), '/');
    $this->basename = $path_is_dir ? '' : trim(pathinfo($path, PATHINFO_BASENAME), '/ ');
  }

  /**
   * Factory method to create an instance of \AKlump\LoftLib\Storage\FilePath
   *
   * @param string $path Full path to directory or file. All parent directories
   *   will be created, unless permissions prevent it.
   * @param array $options Configuration options for the instance:
   *  - autoname string A file extension, e.g. 'pdf' that will be used to
   *   generate a random filename based on tempnam().  $path must be a
   *   directory.
   *  - parents bool Defaults true.  When true, the parent directories will be
   *   automatically created for files when you try to save, copy or move.  If
   *   you set this to false, you must call ::install before calling any
   *   storage methods on a filepath that points to a file whose parent
   *   directories do not exist.
   *  - type int Used to indicate the type when the file or dir does not yet
   * exist. One of self::TYPE_DIR or self::TYPE_FILE.  If this is omitted and
   * an extension can be detected in $path, then this will default to fail,
   * otherwise to dir.
   *
   * @return \AKlump\LoftLib\Storage\FilePath
   *   A new instance of \AKlump\LoftLib\Storage\FilePath.
   */
  public static function create($path, $options = []) {
    $class = __CLASS__;

    return new $class($path, $options);
  }

  /**
   * Create all parent directories on this path.
   *
   * @return \AKlump\LoftLib\Storage\FilePath
   *   Self for chaining.
   */
  public function parents() {
    list($this->dir, $this->basename) = static::ensureDir($this->getPath(), NULL, $this->config['type'] === self::TYPE_DIR);
    $this->type = empty($this->basename) ? static::TYPE_DIR : static::TYPE_FILE;

    return $this;
  }

  /**
   * Generate a tempName string.
   *
   * Does not generate a file.
   *
   * @param string $extension Optional.
   *
   * @return string
   */
  public static function tempName($extension = '') {
    return static::extensionHandler(uniqid('', TRUE), $extension);
  }

  /**
   * Ensure all directories in $path exist, if possible.
   *
   * @param      $path string An absolute filepath.  All parent directories are
   *   created.  If you pass a path to a file, be sure to set $path_is_dir to
   *   false and dirname() will be use on $path.
   * @param int $mode
   *   The file permissions to use when creating directories.
   * @param bool $path_is_dir
   *   Set this to false if $path points to a file, not a directory.
   *
   * @return array - 0 The directory with trailing / removed
   * - 0 The directory with trailing / removed
   * - 1 The basename, if a file; for directories this is null.
   *
   * @throws \AKlump\LoftLib\Code\AKlump\LoftLib\Code\StandardPhpErrorException
   */
  public static function ensureDir($path, $mode = 0777, $path_is_dir = TRUE) {
    if (empty($path)) {
      throw new \InvalidArgumentException("\$path cannot be empty.");
    }
    $mode = $mode ?: 0777;
    $path = trim($path);
    $dirname = rtrim($path_is_dir ? $path : dirname($path), '/');
    $basename = $path_is_dir ? '' : trim(pathinfo($path, PATHINFO_BASENAME), '/ ');
    $path = $dirname . '/' . $basename;

    if (!is_dir($dirname)) {
      $status = NULL;
      try {
        static::throwErrors(function () use (&$status, $dirname, $mode) {
          mkdir($dirname, $mode, TRUE);
        });
      }
      catch (\Exception $exception) {
        static::rethrow($exception, function ($message) use ($path) {
          return "$message: trying to ensure $path";
        });
      }
    }

    return array($dirname, $basename);
  }

  /**
   * Generate a filename based on date
   *
   * @param string $extension , e.g. pdf
   * @param string $format To be used for the DateTime::format.
   * @param \DateTime $datetime You may pass your own object, otherwise now
   *                             in UTC is used.
   *
   * @return string
   */
  public static function dateName($extension = '', $format = NULL, \DateTime $datetime = NULL) {
    $replaceOffsetWithZulu = FALSE;
    if (is_null($format)) {
      $replaceOffsetWithZulu = TRUE;
      $format = 'Y-m-d\TH-i-sO';
    }
    $datetime = $datetime ? $datetime : date_create('now', new \DateTimeZone('UTC'));
    $name = $datetime->format($format);

    // We only do this if format is not provided.
    if ($replaceOffsetWithZulu) {
      $name = preg_replace('/\+0000$/', 'Z', $name);
    }

    return static::extensionHandler($name, $extension);
  }

  protected static function extensionHandler($name, $extension) {
    return $extension ? $name . '.' . ltrim($extension, '.') : $name;
  }

  /**
   * Return the alias or basename if no alias.
   *
   * This can be used for altering the download filename.
   *
   * @return mixed
   */
  public function getAlias() {
    return empty($this->alias) ? $this->getBasename() : $this->alias;
  }

  /**
   * Determine if an alias is set.
   *
   * This can be used since getAlias will always return a value.
   *
   * @return bool
   */
  public function hasAlias() {
    return isset($this->alias);
  }

  /**
   * Set the alias of the file, which means the name which downloads, for
   * example.
   *
   * This has nothing to do with symlinks or os aliases.
   *
   * @param string $alias
   *
   * @return FilePath
   */
  public function setAlias($alias) {
    $this->alias = $alias;

    return $this;
  }

  public function destroy() {
    if ($this->getType() === static::TYPE_FILE) {
      unlink($this->getPath());

      return $this;
    }

    throw new \RuntimeException("Only files can be destroyed.");
  }

  /**
   * Return if this is a dir or a file.
   *
   * @return int
   */
  public function getType() {
    return $this->type;
  }

  /**
   * @return string Path to dir (or file if $this->basename).
   */
  public function getPath() {
    $path = rtrim($this->dir, '/');
    if ($this->basename) {
      $path .= '/' . $this->basename;
    }

    return $path;
  }

  /**
   * For files return parent dir, for directories return self.
   */
  public function getDirName() {
    return $this->dir;
  }

  /**
   * For files only, return the filename (basename without extension)
   *
   * @return string|null
   */
  public function getFileName() {
    return $this->basename ? pathinfo($this->basename, PATHINFO_FILENAME) : NULL;
  }

  /**
   * For files only, return the extension without leading dot.
   *
   * @return string|null
   */
  public function getExtension() {
    return $this->basename ? pathinfo($this->basename, PATHINFO_EXTENSION) : NULL;
  }

  /**
   * Save the $contents to the $this->basename, or provide a new $basename.
   *
   * @return $this
   */
  public function save() {
    $this->validateBasename();

    if ($this->config['parents']) {
      $this->parents();
    }

    if (@file_put_contents($this->getPath(), $this->contents) === FALSE) {
      throw new \RuntimeException("Could not save to " . $this->getPath());
    }

    // When doing this: $dir->put('do')->to('file.txt')->save(); this useage should not change $dir from a $dir.
    if ($dir = $this->getCached('parent_dir')) {
      $this->dir = $dir;
      $this->basename = NULL;
    }

    return $this;
  }

  public function put($contents) {
    $this->contents = $contents;

    return $this;
  }

  public function putJson(array $data) {
    $this->contents = call_user_func_array('json_encode', func_get_args());

    return $this;
  }

  public function to($basename) {
    $return = clone $this;

    return $return->setBasename($basename);
  }

  public function from($basename) {
    $this->validateIsDir(__METHOD__);
    $return = $this->getType() === static::TYPE_DIR ? clone $this : $this;

    return $return->setBasename($basename);
  }

  public function getId() {
    return $this->basename;
  }

  public function load() {
    $this->validateBasename();
    $this->contents = file_get_contents($this->getPath());

    return $this;
  }

  public function get() {
    return $this->contents;
  }

  /**
   * An arguments will be passed to json_decode.
   *
   * @return mixed
   */
  public function getJson() {
    if (($args = func_get_args())) {
      array_unshift($args, $this->contents);

      return call_user_func_array('json_decode', $args);
    }

    return json_decode($this->contents);
  }

  public function putXml(\SimpleXMLElement $data) {
    $this->contents = $data->asXml();

    return $this;
  }

  /**
   * Writes an array of bash code as a bash script with header.
   *
   * @param array $data
   * @param string $header Defaults to '#!/usr/bin/env bash'
   *
   * @return $this
   */
  public function putBash(array $data, $header = '#!/usr/bin/env bash') {
    $this->contents = implode(PHP_EOL, $data);
    if (!preg_match('/' . PHP_EOL . '$/', $this->contents)) {
      $this->contents .= PHP_EOL;
    }
    if (strpos($this->contents, $header) === FALSE) {
      $this->contents = $header . PHP_EOL . $this->contents;
    }

    return $this;
  }

  public function getXml() {
    return new LoftXmlElement($this->contents);
  }

  /**
   * Copy the file at $source into this basename.
   *
   * @param $source
   *
   * @return \AKlump\LoftLib\Component\Storage\FilePath
   */
  public function copy($source) {
    return $this->_copyOrMove('copy', 'copy', $source);
  }

  /**
   * Copy a file in from $source.
   *
   * @param string|\AKlump\LoftLib\Component\Storage\FilePath $source
   *   The source instance to copy from.
   *
   * @return \AKlump\LoftLib\Component\Storage\FilePath
   */
  public function copyFrom($source) {
    if (is_string($source)) {
      $source = new FilePath($source, ['install' => FALSE]);
    }

    $basename = $this->getType() === self::TYPE_FILE ? $this->getBasename() : $source->getBasename();

    return $this->to($basename)
      ->_copyOrMove('copy', 'copy', $source);
  }

  /**
   * Move an uploaded file to $this->basename.
   *
   * @param $source
   *
   * @return \AKlump\LoftLib\Component\Storage\FilePath
   */
  public function upload($source) {
    return $this->_copyOrMove('move', 'move_uploaded_file', $source);
  }

  /**
   * Move $source to $this->basename
   *
   * @param $source
   *
   * @return \AKlump\LoftLib\Component\Storage\FilePath
   */
  public function move($source) {
    return $this->_copyOrMove('move', 'rename', $source);
  }


  /**
   * Change this file's basename.
   *
   * @param string $new_basename
   *   The new basename for the file.
   *
   * @return \AKlump\LoftLib\Storage\FilePath
   *   Self for chaining.
   */
  public function rename($new_name) {
    rename($this->getPath(), $this->getDirName() . "/$new_name");

    return $this;
  }

  /**
   * Move a file from one directory to another.
   *
   * This differs from move in that you do not call to() before because this is
   * copied with the same filename.
   *
   * @param string|\AKlump\LoftLib\Component\Storage\FilePath $source
   *   The source instance to copy from.
   *
   * @return \AKlump\LoftLib\Component\Storage\FilePath
   */
  public function moveFrom($source) {
    if (is_string($source)) {
      $source = new FilePath($source, ['install' => FALSE]);
    }

    return $this->to($source->getBasename())
      ->_copyOrMove('move', 'rename', $source);
  }

  /**
   * Determine if $this->basepath exists.
   *
   * @return bool
   */
  public function exists() {
    return file_exists($this->getPath());
  }

  /**
   * Return headers used to force a download.
   *
   * @return array
   */
  public function getDownloadHeaders() {
    $this->validateBasename();
    $headers = $this->getStreamHeaders() + array(
        'Content-Disposition' => 'attachment; filename="' . $this->getAlias() . '"',
      );

    return $headers;
  }

  /**
   * Return headers used to serve the file.
   *
   * @return array
   */
  public function getStreamHeaders() {
    $this->validateBasename();
    $path = $this->getPath();
    $headers = array();
    $headers['Content-Type'] = $this->getMimeType();
    $headers['Content-Length'] = filesize($path);

    return $headers;
  }

  /**
   * For files only, return the basename
   *
   * @return string|null
   */
  public function getBasename() {
    return !empty($this->basename) ? $this->basename : NULL;
  }

  /**
   * Return the mime type of the file.
   */
  public function getMimeType() {
    $mime = $this->getCached('mime');
    if (!$mime) {
      // I've found this to be more reliable than both: mime_content_type and finfo class. 2017-03-25T09:11, aklump
      $test = new \Mimey\MimeTypes;
      $mime = $test->getMimeType($this->getExtension());
      $this->setCached('mime', $mime);
    }

    return $mime;
  }

  /**
   * Return a flat array of all children files of directory as FilePath
   * objects.
   *
   * @param string $matchRegEx
   * @param string $excludeRegEx
   *
   * @return \AKlump\LoftLib\Component\Storage\FilePath|\AKlump\LoftLib\Component\Storage\FilePathCollection|null
   */
  public function children($matchRegEx = '', $excludeRegEx = '') {
    return $this->descendents($matchRegEx, $excludeRegEx, 1);
  }

  /**
   * Recursively list files and folders.
   *
   * Limit the recursion level by passing a numeric argument > 0. The order of
   * the level is not important, since it is the only numeric argument passed.
   * Pass one non-numeric regex string and it will match. Pass a second
   * non-numeric string and it will exclude.
   *
   *
   * @code
   *
   *  // Limit to 1 level deep
   *  descendents(1, '/\.pdf$/);
   *
   *  // Include only pdfs
   *  descendents('/\.pdf$/);
   *
   *  // Exclude all pdfs
   *  descendents(''. '/\.pdf$/);
   * @endcode
   *
   * @return \AKlump\LoftLib\Component\Storage\FilePathCollection|null|static
   */
  public function descendents() {
    $options = [];
    $assign = function ($key, $value) use (&$options) {
      if (!array_key_exists($key, $options)) {
        $options[$key] = $value;

        return TRUE;
      }

      return FALSE;
    };
    foreach (func_get_args() as $value) {
      if (!in_array($type = gettype($value), ['integer', 'string'])) {
        throw new \InvalidArgumentException("Arguments must be of type: integer or string. $type received.");
      }
      if (is_int($value)) {
        $assign(0, $value * 1);
      }
      elseif (!$assign(1, strval($value))) {
        $assign(2, strval($value));
      }
    }
    $options += [0 => 0, 1 => '', 2 => ''];
    $this->validateIsDir(__METHOD__);

    return $this->_getFilesRecursively($this->getPath(), $options);
  }

  /**
   * Return the contents of a file or a directory listing.
   *
   * @return array|bool|string
   */
  public function getContents() {
    return $this->getType() === self::TYPE_FILE ? file_get_contents($this->getPath()) : $this->children()
      ->all();
  }

  protected function validateIsDir($method) {
    if ($this->getType() !== static::TYPE_DIR) {
      throw new \RuntimeException("$method() allowed only on directories.");
    }
  }

  protected function validateIsFile($method) {
    if ($this->getType() !== static::TYPE_FILE) {
      throw new \RuntimeException("$method() allowed only on files.");
    }
  }

  protected function validateBasename() {
    if (empty($this->basename)) {
      throw new \RuntimeException("You must use to() to specify a basename for the file.");
    }
  }

  /**
   * To change this publically, you need to use to()
   *
   * @param $basename
   *
   * @return $this
   */
  protected function setBasename($basename) {
    if (empty($basename)) {
      throw new \InvalidArgumentException("\$basename cannot be empty.");
    }
    if (strpos($basename, '/') !== FALSE) {
      throw new \InvalidArgumentException("\"$basename\" cannot be a path, only a filename.");
    }
    $this->clearCached();
    $this->basename = trim($basename);
    $this->config['type'] = self::TYPE_FILE;

    return $this;
  }

  /**
   * Helper, do not call directly.
   */
  protected function _copyOrMove($op, $function, $source) {
    $source = is_object($source) && method_exists($source, 'getPath') ? $source->getPath() : $source;
    if (!file_exists($source)) {
      throw new \RuntimeException("\"$source\" does not exist; can't $op.");
    }
    $this->validateBasename();
    if ($this->config['parents']) {
      $this->parents();
    }
    if (!$function($source, ($d = $this->getPath()))) {
      throw new \RuntimeException("Could not $op \"$source\" to \"$d\"");
    }

    return $this;
  }

  /**
   * Helper function
   *
   * @param string $path
   * @param array $options
   *  - 0 int Maximum levels down to recurse
   *  - 1 string The match regex
   *  - 2 string The omit regex
   * @param null $files
   * @param int $level
   *
   * @return \AKlump\LoftLib\Component\Storage\FilePathCollection|null
   * @see FilePath::getFilesRecursively()
   *
   */
  private function _getFilesRecursively($path, $options, &$files = NULL, &$level = 0) {
    if (is_null($files)) {
      $files = new FilePathCollection();
    }
    $obj = function ($path) {
      $class = get_class($this);

      return new $class($path);
    };
    ++$level;
    if (is_dir($path)) {
      $dir = opendir($path . "/.");
      list($levels) = $options;
      while ($item = readdir($dir)) {
        if (($item == "." || $item == "..")) {
          continue;
        }

        $fullPath = $path . "/" . $item;
        $files->push($obj($fullPath));

        // Descend into directories if levels allows.
        if (is_dir($fullPath) && (empty($levels) || $level < $levels)) {
          $this->_getFilesRecursively($fullPath, $options, $files, $level);
        }
      }
    }
    --$level;
    if ($level === 0) {
      $files = $files->filter(function ($value) use ($options) {
        list(, $match, $omit) = $options;
        $fullPath = $value->getPath();
        $doesMatch = !$match || preg_match($match, $fullPath);

        return $doesMatch && (!$omit || !preg_match($omit, $fullPath));
      })->sort(function ($a, $b) {
        return $a->getPath() > $b->getPath();
      })->values();
    }

    return $files;
  }

  /**
   * Return a hash of the file contents.
   *
   * @return string
   *   The hashed file contents.
   */
  public function getHash() {
    return md5_file($this->getPath());
  }
}
