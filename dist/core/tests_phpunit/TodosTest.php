<?php

namespace AKlump\LoftDocs\Tests;

use PHPUnit\Framework\TestCase;

/**
 * @covers
 */
class TodosTest extends TestCase {

  public function testParseTodosWithPrefix() {
    $subject = "- [ ] do the thing";
    $todos = \parse_todos($subject, 'Step One:');
    $this->assertSame("- [ ] Step One:do the thing", reset($todos));
  }

  public function test_flatten_todos() {
    $subject = array(
      '- [ ] This is number two @w2',
      '- [ ] This is number two @w2',
      '- [ ] This is number three @w3',
    );
    $control = implode("\n", $subject) . "\n";
    $this->assertSame($control, \flatten_todos($subject));
  }

  public function test_get_weight() {
    $this->assertSame(5, \get_weight('- [ ] @w5 This is a todo with a weight'));
    $this->assertSame(7, \get_weight('- [ ] @w5 This is a todo with a weight @w7'));
    $this->assertSame(7, \get_weight('- [ ] This is a todo with a weight @w7'));
    $this->assertSame(7.34, \get_weight('- [ ] This is a todo with a weight @w7.34'));
    $this->assertSame(0, \get_weight('- [ ] This is a todo with a weight'));
  }

  public function testSortTodosRemovesDuplicates() {
    $subject = array(
      '- [ ] This is number two @w2',
      '- [ ] This is number two @w2',
      '- [ ] This is number three @w3',
    );
    $control = array(
      '- [ ] This is number two @w2',
      '- [ ] This is number three @w3',
    );
    \sort_todos($subject);
    $this->assertSame($control, $subject);
  }

  public function test_sort_todos() {
    $subject = array(
      '- [ ] This is number two @w2',
      '- [ ] This is number one @w1',
      '- [ ] This is number three @w3',
    );
    $control = array(
      '- [ ] This is number one @w1',
      '- [ ] This is number two @w2',
      '- [ ] This is number three @w3',
    );
    \sort_todos($subject);
    $this->assertSame($control, $subject);
  }

  public function test_parse_todos() {
    $subject = <<<EOD
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla at massa sed nulla consectetur malesuada.
- [ ] This is number two @w2
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla at massa sed nulla consectetur malesuada.
- [ ] This is number one @w1
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla at massa sed nulla consectetur malesuada.
And then this is: - [ ] This is number three @w3
EOD;

    $control = array(
      '- [ ] This is number two @w2',
      '- [ ] This is number one @w1',
      '- [ ] This is number three @w3',
    );
    $this->assertSame($control, \parse_todos($subject, '', FALSE));

    // Empty array on empty string
    $this->assertSame(array(), \parse_todos(''));
  }
}
