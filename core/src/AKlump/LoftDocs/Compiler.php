<?php

namespace AKlump\LoftDocs;

use AKlump\LoftLib\Storage\FilePath;

/**
 * Provide compiling functionality
 */
class Compiler {

  /**
   * The path to the cache directory.
   *
   * @var \AKlump\LoftLib\Storage\FilePath
   */
  protected $pathToDynamicSourceFiles;

  public function __construct(FilePath $dynamic_source) {
    $this->pathToDynamicSourceFiles = $dynamic_source;
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
    if (substr($basename, 0, 1) !== '_') {
      throw new \InvalidArgumentException("Include files must begin with an underscore; did you mean _\"" . $basename . '"?');
    }

    return $this->addSourceFile($basename, $contents);
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

}
