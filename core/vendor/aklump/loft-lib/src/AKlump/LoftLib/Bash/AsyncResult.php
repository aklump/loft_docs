<?php

namespace AKlump\LoftLib\Bash;

/**
 * Returned when executing async bash commands.
 */
class AsyncResult {

  /**
   * The filepath to the capture file.
   *
   * @var string|null
   */
  protected $captureOutputPath = NULL;

  /**
   * AsyncResult constructor.
   *
   * @param string $capture_output_filepath
   *   The path where the async command will save it's output.
   */
  public function __construct($capture_output_filepath) {
    $this->captureOutputPath = $capture_output_filepath;
  }

  /**
   * Return the command output.
   *
   * @return bool|string
   *   The output from the async command.
   */
  public function getOutput() {
    return file_exists($this->captureOutputPath) ? (string) file_get_contents($this->captureOutputPath) : FALSE;
  }

  /**
   * Ensure the capture file does not exists, deleting if necessary.
   *
   * @return bool
   *   True if the capture output file no longer exists.
   */
  public function flush() {
    return !file_exists($this->captureOutputPath) || unlink($this->captureOutputPath);
  }

}
