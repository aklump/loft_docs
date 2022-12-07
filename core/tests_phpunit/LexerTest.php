<?php

namespace AKlump\LoftDocs\Tests;

use AKlump\LoftDocs\Lexer;
use AKlump\LoftDocs\Parser;
use AKlump\LoftDocs\TokenTypes;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AKlump\LoftDocs\Lexer
 */
class LexerTest extends TestCase {

  public function testAtTodoIsNotDetectedAsAnInternalLink() {
    $source = "<p>To truly output 'Foo' when there is no entity... how is that achieved? @todo.</p>";
    $lexer = new Lexer($source);
    $tokens = $lexer->getTokens();
    $parser = new Parser($tokens);
    $tokens = $parser->getTokensByType(TokenTypes::INTERNAL_LINK);
    $this->assertCount(0, $tokens);
  }

  public function testInternalLinksIgnoredWhenInsideHtmlCodeBlock() {
    $source = <<<EOD
    sure you are running at least version 0.18 of <a href="https://github.com/aklump/loft_deploy">Loft Deploy</a>.</strong></p>
      
    <pre><code class="yaml">local:
      database:
        lando: "@drupal"
      drupal:
        root: web
    </code></pre>
    
    <h2>Configuration</h2>
    
    <ol>
    <li>In <em>.lando</em> you need to add more database services for each branch you wish  
    EOD;
    $lexer = new Lexer($source);
    $tokens = $lexer->getTokens();
    $parser = new Parser($tokens);
    $tokens = $parser->getTokensByType(TokenTypes::INTERNAL_LINK);
    $this->assertCount(0, $tokens);
  }

  public function testInternalLinksIgnoredWhenInsideCodeBlock() {
    $source = <<<EOD
    Lorem
    
    ```php
    \$string = '@drupal'    
    \$string2 = '@drupal:/foo/bar/baz'
    ```
    Lorem ipsum dolor sit amet, consectetur `@page1` elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.    
    EOD;
    $lexer = new Lexer($source);
    $tokens = $lexer->getTokens();
    $parser = new Parser($tokens);
    $tokens = $parser->getTokensByType(TokenTypes::INTERNAL_LINK);
    $this->assertCount(0, $tokens);
  }

  public function testLocatesInternalLinksInMarkup() {
    $source = "Lorem ipsum\n[click](@page2:part4)\n\n<@page3> and then some more things.";
    $lexer = new Lexer($source);
    $tokens = $lexer->getTokens();
    $parser = new Parser($tokens);
    $tokens = $parser->getTokensByType(TokenTypes::INTERNAL_LINK);

    $this->assertCount(2, $tokens);
    $this->assertSame('page2', (new Parser($tokens))->getTokensByType(TokenTypes::PAGE_ID)[0]['value']);
    $this->assertSame('part4', (new Parser($tokens))->getTokensByType(TokenTypes::HTML_ID)[0]['value']);
    $this->assertSame('page3', (new Parser($tokens))->getTokensByType(TokenTypes::PAGE_ID)[1]['value']);
    $this->assertNull((new Parser($tokens))->getTokensByType(TokenTypes::HTML_ID)[1]['value']);
  }

  public function testTodosAreLocatedAsExpected() {
    $source = "- [ ] Lorem Ipsum\n- [x] Dolar sit amet\n\nLearn more...";
    $lexer = new Lexer($source);
    $tokens = $lexer->getTokens();
    $parser = new Parser($tokens);
    $tokens = $parser->getTokensByType(TokenTypes::TODO);
    $this->assertCount(2, $tokens);

    $this->assertSame('Lorem Ipsum',
      (new Parser($tokens))->getTokensByType(TokenTypes::ACTION)[0]['value']);
    $this->assertFalse((new Parser($tokens))->getTokensByType(TokenTypes::COMPLETED)[0]['value']);
    $this->assertSame('Dolar sit amet',
      (new Parser($tokens))->getTokensByType(TokenTypes::ACTION)[1]['value']);
    $this->assertTrue((new Parser($tokens))->getTokensByType(TokenTypes::COMPLETED)[1]['value']);
  }

  public function testIdsInCodeSectionAreIgnored() {
    $source = <<<EOD
    ### Loft Deploy
    This technique works also with _Loft Deploy_ when you provide Drupal's webroot
    via `local.drupal.root`, and set `local.database.lando` to `@drupal`.  **Make sure you are running at least version 0.18 of [Loft Deploy](https://github.com/aklump/loft_deploy).**
    
    ```yaml
    local:
      database:
        lando: "@drupal"
      drupal:
        root: web
    ```
    
    ## Configuration    
    EOD;

    $lexer = new Lexer($source);
    $tokens = $lexer->getTokens();
    $parser = new Parser($tokens);
    $ids = $parser->getTokensByType(TokenTypes::PAGE_ID);
    $this->assertEmpty($ids);
  }

}
