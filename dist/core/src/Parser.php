<?php

namespace AKlump\LoftDocs;

class Parser {

  protected $tokens;

  /**
   * @param array $tokens
   *
   * @see \AKlump\LoftDocs\Lexer::getTokens()
   */
  public function __construct(array $tokens) {
    $this->tokens = $tokens;
  }

  public function getTokens(): array {
    return $this->tokens;
  }

  /**
   * Return all tokens of a given type recursively.
   *
   * @param string $type
   *
   * @return array
   *   The tokens of $type, if found.
   *
   * @see \AKlump\LoftDocs\TokenTypes
   */
  public function getTokensByType(string $type): array {
    return $this->doTokenQuery($this->tokens, ['type' => $type]);
  }

  /**
   * Map all tokens recursively.
   *
   * @param callable $callback
   *
   * @return $this
   */
  public function mapTokens(callable $callback): self {
    $this->doTokenMap($this->tokens, $callback);

    return $this;
  }

  private function doTokenMap(array &$input, callable $callback) {
    if (is_numeric(key($input))) {
      foreach ($input as &$t) {
        self::doTokenMap($t, $callback);
      }
    }
    else {
      $input = $callback($input);
      if (isset($input['value']) && is_array($input['value'])) {
        self::doTokenMap($input['value'], $callback);
      }
    }
  }

  /**
   * Helper function for token queries.
   *
   * @param array $input
   *   The token array to search.
   * @param array $query
   *   Tokens will be returned if they have the same values for all provided
   *   keys in $query.
   * @param array $results
   *   Internal use only.
   *
   * @return array
   *   The query result
   */
  private function doTokenQuery(array $input, array $query, array &$results = []): array {
    if (is_numeric(key($input))) {
      foreach ($input as $t) {
        self::doTokenQuery($t, $query, $results);
      }
    }
    else {
      $intersection = array_intersect_key($input, $query);
      if ($intersection == $query) {
        $results[] = $input;
      }
      if (isset($input['value']) && is_array($input['value'])) {
        self::doTokenQuery($input['value'], $query, $results);
      }
    }

    return $results;
  }
}
