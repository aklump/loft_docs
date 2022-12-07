<?php

namespace AKlump\LoftDocs;

class Lexer {

  protected string $stream;

  protected array $lexemes = [];

  protected bool $skipLexeme;

  protected array $tokens = [];

  protected string $node = '';

  /**
   * True when the lexemes are within a source code block.
   *
   * @var bool
   */
  protected bool $insideCodeBlock = FALSE;

  public function __construct(string $stream) {
    $this->stream = $stream . PHP_EOL;
  }

  public function getTokens(): array {
    $this->lexemes = mb_str_split($this->stream);
    $length = count($this->lexemes);

    $this->possibleTypes = [];
    $this->node = '';
    $this->isTokenReady = FALSE;


    for ($this->pointer = 0; $this->pointer < $length; ++$this->pointer) {
      $lexeme = $this->lexemes[$this->pointer];
      $this->skipLexeme = FALSE;
      $this->isTokenReady = FALSE;

      // This chain should do the following when inspecting the $lexeme.
      // - Mutate $this->skipLexeme
      // BE CAREFUL WITH ORDER!
      $this
        ->handleCodeBlocks($lexeme)
        ->handleTodos($lexeme)
        ->handleInternalLinks($lexeme);

      if (!$this->skipLexeme) {
        $this->node .= $lexeme;
      }
    }

    return $this->tokens;
  }

  private function getNextLexeme() {
    $this->lexemes[$this->pointer + 1] ?? NULL;
  }

  private function getRemainingStream(): string {
    $lexemes = $this->lexemes;

    return implode('', array_splice($lexemes, $this->pointer));
  }

  private function handleCodeBlocks($lexeme) {
    if (!preg_match('/[\s`\<\>]/', $lexeme)) {
      return $this;
    }

    // This looks to see if this is the start of inline code.
    $is = FALSE;
    if ('`' === $lexeme) {
      $regex = '/`[^\n]+`/';
      if (!$this->insideCodeBlock) {
        $is = preg_match($regex, $this->getRemainingStream());
        $matches = [NULL, $this->node];
      }
      else {
        $is = preg_match($regex, "`$this->node");
      }
    }

    $is = $is || preg_match('/(.*)`{3}(.*)\n/s', $this->node . $lexeme, $matches);
    $is = $is || preg_match('/(.*)(?:<code class="(.+)">|<code>|<\/code>)/s', $this->node . $lexeme, $matches);
    if ($is) {
      $this->insideCodeBlock = !$this->insideCodeBlock;
      if ($this->insideCodeBlock) {
        if (!empty($matches[1])) {
          $this->tokens[] = $this->getToken(TokenTypes::SOURCE_CODE, $matches[1] ?? NULL);
        }
        $this->tokens[] = $this->getToken(TokenTypes::SOURCE_CODE_START, $matches[2] ?? NULL);
      }
      else {
        if (!empty($matches[1])) {
          $this->tokens[] = $this->getToken(TokenTypes::COPY, $matches[1] ?? NULL);
        }
        $this->tokens[] = $this->getToken(TokenTypes::SOURCE_CODE_END, NULL);
      }
      $this->node = '';
      $this->skipLexeme = TRUE;
    }

    return $this;
  }

  /**
   * Check if $string is a todo
   *
   * @param string $string
   * @param array &$token
   *   Pass this in and it will be set if $string is a token.
   *
   * @return bool
   */
  private function isTodo(string $string, &$token = []): bool {
    $is = preg_match('/\-\s*\[([ x]?)\]\s*(.+)/i', $string, $matches);
    if ($is) {
      $token = [
        $this->getToken(TokenTypes::COMPLETED, boolval(trim($matches[1]))),
        $this->getToken(TokenTypes::ACTION, $matches[2]),
      ];
      $token = $this->getToken(TokenTypes::TODO, $token);
    }

    return boolval($is);
  }

  /**
   * @param string $string
   * @param $tokens
   *
   * @return bool
   *
   * @code
   *
   * These are the URL portion of markdown links.
   * (@PAGE_ID:HTML_ID)
   * (@PAGE_ID)
   *
   * These are markdown URLs.
   * <@PAGE_ID:HTML_ID>
   * <@PAGE_ID>
   * @endcode
   */
  private function isInternalLink(string $string, &$tokens = []): bool {
    $is = preg_match('/(.*)\(\@([^\n:]+):?([^\n]+)?\)/', $string, $matches);
    $is = $is || preg_match('/(.*)<\@([^\n:]+):?([^\n]+)?>/s', $string, $matches);
    if ($is) {
      if (!empty($matches[1])) {
        $tokens[] = $this->getToken(TokenTypes::COPY, $matches[1]);
      }
      $tokens[] = $this->getToken(TokenTypes::INTERNAL_LINK, [
        $this->getToken(TokenTypes::PAGE_ID, $matches[2]),
        $this->getToken(TokenTypes::HTML_ID, $matches[3] ?? NULL),
      ]);
    }

    return boolval($is);
  }

  private function handleTodos($lexeme) {
    $token = [];
    if (!$this->insideCodeBlock
      && PHP_EOL === $lexeme
      && $this->isTodo($this->node, $token)) {
      $this->tokens[] = $token;
      $this->node = '';
      $this->skipLexeme;
    }

    return $this;
  }

  private function handleInternalLinks($lexeme) {

    // Look to see if our lexeme is closing out an internal link
    if ($this->insideCodeBlock || !in_array($lexeme, [')', '>'])) {
      return;
    }

    $tokens = [];
    if ($this->isInternalLink($this->node . $lexeme, $tokens)) {
      $this->tokens = array_merge($this->tokens, $tokens);
      $this->node = '';
      $this->skipLexeme;
    }

    return $this;
  }

  public static function getToken(string $type, $value) {
    return [
      'type' => $type,
      'value' => $value,
    ];
  }

}
