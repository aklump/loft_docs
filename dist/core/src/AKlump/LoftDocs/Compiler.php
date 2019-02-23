<?php

namespace AKlump\LoftDocs;

use AKlump\Data\Data;
use AKlump\LoftLib\Storage\FilePath;

/**
 * Provide compiling functionality
 */
class Compiler {

  /**
   * The path to the original source directory.
   *
   * @var \AKlump\LoftLib\Storage\FilePath
   */
  protected $pathToStaticSourceFiles;

  /**
   * The path to the cache/source directory.
   *
   * @var \AKlump\LoftLib\Storage\FilePath
   */
  protected $pathToDynamicSourceFiles;

  /**
   * The path to the outline file.
   *
   * @var \AKlump\LoftLib\Storage\FilePath
   */
  protected $pathToOutline;

  public function __construct(FilePath $static_source, FilePath $dynamic_source, Filepath $outline) {
    $this->pathToStaticSourceFiles = $static_source;
    $this->pathToDynamicSourceFiles = $dynamic_source;
    FilePath::ensureDir($this->pathToDynamicSourceFiles->getPath());
    $this->pathToOutline = $outline;
    if (!$outline->exists()) {
      throw new \RuntimeException("\$outline does not exist.");
    }
  }

  /**
   * Create a compile-time only source file.
   *
   * Use this from hooks to create dynamic content.  This is cached and
   * destroyed before the next compile.  For include files you should use
   * ::addInclude instead.
   *
   * @param string $basename
   *   The basename of the source file.
   * @param string $contents
   *   The file contents.
   *    *
   *
   * @return \AKlump\LoftLib\Storage\FilePath
   *   The source file
   *
   * @see addInclude
   */
  public function addSourceFile($basename, $contents) {
    return $this->pathToDynamicSourceFiles->to($basename)
      ->put($contents)
      ->save();
  }

  /**
   * Create a dynamic include file.
   *
   * This file will be accessible during the build process.  Since it's an
   * include file, it will not appear as it's own page and will not be indexed.
   *  For indexed or page content use ::addSourceFile.
   *
   * @param string $basename
   *   The basename of the file, which must begin with underscore, e.g.
   *   "_headline.md".
   * @param string $contents
   *   The file contents.
   *
   * @return \AKlump\LoftLib\Storage\FilePath
   *   The source file
   *
   * @throws \InvalidArgumentException
   *   If $basename does not begin with an underscore.
   */
  public function addInclude($basename, $contents) {
    return $this->validateIncludeBasename($basename)
      ->addSourceFile($basename, $contents);
  }

  /**
   * Check that $basename begins with an underscore.
   *
   * @param string $basename
   *   The basename to load.
   *
   * @return $this
   *   Self for chaining.
   */
  private function validateIncludeBasename($basename) {
    if (substr($basename, 0, 1) !== '_') {
      throw new \InvalidArgumentException("Include files must begin with an underscore; did you mean _\"" . $basename . '"?');
    }

    return $this;
  }

  /**
   * Get an instance of FilePath for an include file.
   *
   * We will look in dynamic files first, then static.
   *
   * @param string $basename
   *   The basename.
   *
   * @return \AKlump\LoftLib\Storage\FilePath
   *   The include filepath instance.
   */
  public function getInclude($basename) {
    $this->validateIncludeBasename($basename);
    $include = $this->pathToDynamicSourceFiles->to($basename);
    if (!$include->exists()) {
      $include = $this->pathToStaticSourceFiles->to($basename);
    }

    return $include;
  }

  /**
   * Return all source files from a directory.
   *
   * @param string $path_to_dir
   *   A directory to scan for source files.
   *
   * @return array
   *   Each element has keys:
   *   - path
   *   - source
   *   - compiled
   */
  public static function indexSourceFiles($path_to_dir) {
    $index = is_dir($path_to_dir) ? scandir($path_to_dir) : array();

    return empty($index) ? $index : array_values(array_map(function ($basename) use ($path_to_dir) {
      $info = pathinfo($path_to_dir . "/$basename");
      $info += [
        'path' => $info['dirname'] . '/' . $info['basename'],
        'filename_compiled' => self::getCompiledFilenameFromSourcePath($basename),
      ];

      return $info;
    }, array_filter($index, function ($item) {
      return !in_array($item, [
          '.',
          '..',
          '.DS_Store',
          'search--results.md',
        ]) && substr($item, 0, 1) !== '_';
    })));
  }

  /**
   * Get the compiled filename from a source file.
   *
   * @param string $source_path
   *   Expected "file.md" or "file.twig.md".
   *
   * @return string
   *   The compiled filename, without extension, e.g. "file".
   */
  public static function getCompiledFilenameFromSourcePath($source_path) {
    return preg_replace('/\.twig$/', '', pathinfo($source_path, PATHINFO_FILENAME));
  }

  /**
   * Detect if a filename points to a markdown file.
   *
   * @param  string $path
   *
   * @return bool
   */
  public static function pathIsSection($path) {
    $ext = pathinfo(strtolower($path), PATHINFO_EXTENSION);
    $valid = get_markdown_extensions();
    //@todo want to be able to show text files?
    // return in_array($ext, array('txt') + get_markdown_extensions());

    return in_array($ext, $valid);
  }

  /**
   * Get a settings value.
   *
   * Settings are reported in the outline file under the key `settings`.
   *
   * @param string $setting_key
   *   The setting name/key, e.g., "tasklist.aggregate".
   * @param null $default
   *   The default value
   *
   * @return mixed
   *   The value of the setting or $default.
   */
  public function getSetting($setting_key, $default = NULL) {
    $g = new Data();
    $json = $this->pathToOutline->load()->getJson(TRUE);
    $json += ['settings' => []];

    return $g->get($json, 'settings.' . $setting_key, $default);
  }

  /**
   * Add variables which are used in the twig rendering.
   *
   * @param array $variables
   *   An associative array of replacement vars.
   *
   * @return $this
   *   Self, for chaining.
   */
  public function addVariables(array $variables) {
    $files = $this->pathToDynamicSourceFiles->to('_variables.json');
    if ($files->exists()) {
      $variables += $files->load()->getJson();
    }
    $files->putJson($variables)->save();

    return $this;
  }

  /**
   * Return the variables for use with the twig rendering.
   *
   * @return array
   *   The twig template data.
   */
  public function getVariables() {
    $files = $this->pathToDynamicSourceFiles->to('_variables.json');
    $variables = [];
    if ($files->exists()) {
      $variables += $files->load()->getJson(TRUE);
    }

    return $variables;
  }

}
