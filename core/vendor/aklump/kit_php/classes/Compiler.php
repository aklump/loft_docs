<?php
/**
 * @file
 * Defines the Compiler class
 *
 * @ingroup kit_php
 * @{
 */
namespace aklump\kit_php;

/**
 * Interface CompilerInterface
 */
interface CompilerInterface extends KitInterface {
  public function setSourceDirectory($directory);
  public function getSourceDirectory();
  public function setOutputDirectory($directory);
  public function getOutputDirectory();

  /**
   * Return all .kit files in the source dir
   *
   * @return array
   *   keys are the filenames
   *   values are the complete paths
   */
  public function getKitFiles();
}

/**
 * Class Compiler
 */
class Compiler extends Kit implements CompilerInterface {

  protected $source_dir, $output_dir, $imports, $kit_files;

  /**
   * Constructor
   *
   * @param string $source_dir
   * @param string $output_dir
   */
  public function __construct($source_dir = NULL, $output_dir = NULL) {
    $this->setSourceDirectory($source_dir);
    $this->setOutputDirectory($output_dir);
    $this->imports = array();
  }

  public function __destruct() {
    // Replace all the kit files with their originals
    if ($this->source_dir) {
      $files = scandir($this->source_dir);
      foreach ($files as $key => $file) {
        if (preg_match('/(.*?\.kit)\.orig$/', $file, $matches)) {
          unlink($this->source_dir . '/' . $matches[1]);
          rename($this->source_dir . '/' . $matches[0], $this->source_dir . '/' . $matches[1]);
        }
      }
    }
  }

  /**
   * Check and/or recursively create a directory
   */
  protected function checkDir($dir, $create) {
    if ($dir && $create && !is_dir($dir)) {
      mkdir($dir, 0700, TRUE);
    }

    return is_dir($dir);
  }

  /**
   * Write a file to the source or output directory
   *
   * @param string $contents
   * @param string $file
   * @param string $destination
   *   (Optional) Defaults to 'output'. The other option is 'source'.
   */
  protected function writeFile($contents, $file, $destination = 'output') {
    $dir = $destination === 'source' ? $this->source_dir : $this->output_dir;

    if (strpos($file, $dir) === 0) {
      $file = trim(substr($file, strlen($dir)), '/');
    }

    // Make an orig backup
    if ($destination === 'source' && !file_exists($dir . '/' . $file . '.orig')) {
      copy($dir . '/' . $file, $dir . '/' . $file . '.orig');
    }

    $fp = fopen($dir . '/' . $file, 'w');
    fwrite($fp, $contents);
    fclose($fp);
  }

  public function setSourceDirectory($directory, $create = TRUE){
    if ($this->checkDir($directory, $create)) {
      $this->source_dir = $directory;
    }

    return $this;
  }

  public function getSourceDirectory() {
    return $this->source_dir;
  }

  public function setOutputDirectory($directory, $create = TRUE){
    if ($this->checkDir($directory, $create)) {
      $this->output_dir = $directory;
    }

    return $this;
  }

  public function getOutputDirectory() {
    return $this->output_dir;
  }

  /**
   * Recursively scan a directory for .kit files
   *
   * @param string $dir
   */
  public function _getKitFiles($dir) {
    $files = scandir($dir);
    foreach ($files as $key => $file) {
      if ($file === '.' || $file === '..') {
        continue;
      }
      $path = $dir . '/' . $file;
      if (is_dir($path)) {
        $this->_getKitFiles($path);
      }
      elseif (preg_match('/\.kit$/', $file)) {
        $this->kit_files[$file] = $dir . '/' . $file;
      }
    }
  }

  public function getKitFiles() {
    $this->kit_files = array();
    $this->_getKitFiles($this->source_dir);

    return $this->kit_files;
  }

  public function apply() {
    if ($files = $this->getKitFiles()) {
      foreach ($files as $file => $path) {
        $import = new Imports($path);

        // Make a backup of the file.
        $this->writeFile($import->getSource(), $file, 'source');

        // Now store the compiled file
        $this->writeFile($import->apply(), $file, 'source');

        $this->imports += $import->getImports();
      }
    }

    $files = array_diff_key($files, $this->imports);
    foreach ($files as $file => $path) {
      $variables = new Variables($path, TRUE);
      $variables->extract();
      $result = $variables->apply();
      $file = preg_replace('/\.kit$/', '.html', $file);
      $this->writeFile($result, $file);
    }

    return $result;
  }
}



/** @} */ //end of group: kit_php
