<?php
/**
 * @file
 * PHPUnit tests for the Bash class
 */

namespace AKlump\LoftLib\Bash;

use PHPUnit\Framework\TestCase;

class BashTest extends TestCase {

  public function testBashWithArgumentsWrapsWithDoubleQuotes() {
    $result = Bash::exec('test() { echo $#; } && test', [
      'alpha',
      'bravo charlie',
    ]);
    $this->assertEquals(2, $result);
  }

  public function testDefaults() {
    $obj = new Bash(array());
    $this->assertSame('theme', $obj->getParam('to', 'theme'));
    $this->assertSame('.', $obj->getArg(0, '.'));
  }

  public function testGetArg() {
    $obj = new Bash(array('do', 're', 'mi', 5));
    $this->assertSame('do', $obj->getArg(0));
    $this->assertSame('re', $obj->getArg(1));
    $this->assertSame('mi', $obj->getArg(2));
    $this->assertInternalType('int', $obj->getArg(3));
  }

  public function testGetParam() {
    $obj = new Bash(array('--to=theme', '--count=5'));
    $this->assertNull($obj->getParam('out'));
    $this->assertSame('theme', $obj->getParam('to'));
    $this->assertInternalType('string', $obj->getParam('to'));
    $this->assertInternalType('int', $obj->getParam('count'));
  }

  public function testHasParam() {
    $obj = new Bash(array('--save'));
    $this->assertFalse($obj->hasParam('out'));
    $this->assertTrue($obj->hasParam('save'));
  }

  public function testHasFlag() {
    $obj = new Bash(array('-f'));
    $this->assertFalse($obj->hasFlag('r'));
    $this->assertTrue($obj->hasFlag('f'));
  }

  /**
   * Provides data for testGetters.
   */
  public function dataForTestGettersProvider() {
    $tests = array();
    $tests[] = array(
      array(
        'args' => array('do', 're'),
        'params' => array(),
        'flags' => array(),
      ),
      array('do', 're'),
    );
    $tests[] = array(
      array(
        'args' => array('.'),
        'params' => array('out' => '/do/re', 'to' => 'theme'),
        'flags' => array(),
      ),
      array('.', '--out=/do/re', '--to=theme'),
    );
    $tests[] = array(
      array(
        'args' => array('do'),
        'params' => array('mi' => 'fa'),
        'flags' => array('r'),
      ),
      array('do', '-r', '--mi=fa'),
    );

    $tests[] = array(
      array(
        'args' => array('do'),
        'params' => array('mi' => 'fa'),
        'flags' => array('r'),
      ),
      array('do', '-r', '--mi=fa'),
    );

    return $tests;
  }

  /**
   * @dataProvider dataForTestGettersProvider
   */
  public function testGetters($controls, $subject) {
    $obj = new Bash($subject);
    $this->assertSame($controls['args'], $obj->getArgs());
    $this->assertSame($controls['params'], $obj->getParams());
    $this->assertSame($controls['flags'], $obj->getFlags());
  }

}
