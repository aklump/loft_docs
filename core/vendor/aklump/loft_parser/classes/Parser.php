<?php
/**
 * @file
 * Defines the abstract parse class
 *
 * @ingroup loft_parser
 * @{
 */
namespace aklump\loft_parser;

/**
 * Interface Parser
 */
interface ParserInterface {

  /**
   * Parse and return the results
   *
   * @return string
   */
  public function parse();

  public function setSource($source);

  public function setSourceFromFile($path);

  public function getSource();

  public function addAction(ParseActionInterface $action);

  /**
   * Return all actions used in the parse method
   *
   * @return array
   *   An array of ParseAction objects.
   */
  public function getActions();
}

/**
 * Class Parser
 */
class Parser implements ParserInterface {

  protected $actions = array();
  public $parsed = '';

  /**
   * Constructor
   *
   * @param string $source
   * @param string $source
   *   (Optional) Defaults to NULL.
   * @param bool $is_path.
   *   (Optional) Defaults to FALSE.  Set to TRUE if $source is a filepath.
   */
  public function __construct($source = NULL, $is_path = FALSE) {
    if ($is_path) {
      $this->setSourceFromFile($source);
    }
    else {
      $this->setSource($source);
    }
  }

  public function setSource($source) {
    $this->source = $source;

    return $this;
  }

  public function setSourceFromFile($source) {
    if (is_file($source)) {
      $source = file_get_contents($source);
    }
    $this->setSource($source);

    return $this;
  }

  public function getSource() {
    return $this->source;
  }

  public function parse() {
    $parsed = $this->getSource();
    foreach ($this->actions as $action) {
      $action->parse($parsed);
    }

    return $parsed;
  }

  public function addAction(ParseActionInterface $action) {
    $this->actions[] = $action;

    return $this;
  }

  public function getActions() {
    return $this->actions;
  }
}

/** @} */ //end of group: loft_parser
