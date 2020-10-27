<?php

namespace AKlump\LoftLib\Bash;

use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase {

  public function setUp() {
    $this->dependencies = ['config', '___'];
    $this->createObj();
  }

  protected function createObj() {
    list ($prefix, $sep) = $this->dependencies;
    $this->obj = new Configuration($prefix, $sep);
  }

  /**
   * Provides data for testGetVarEvalCode.
   */
  public function dataForTestGetVarEvalCodeProvider() {
    $tests = array();
    $tests[] = array(
      'version="1.0.5"',
      'version',
      '1.0.5',
    );
    $tests[] = array(
      'version=1',
      'version',
      1.0,
    );
    $tests[] = array(
      'version="1.0"',
      'version',
      '1.0',
    );
    $tests[] = array(
      'declare -a robin=()',
      'robin',
      array(),
    );
    $tests[] = array(
      'robin=null',
      'robin',
      NULL,
    );
    $tests[] = array(
      'declare -a robin=("whack")',
      'robin',
      array('whack'),
    );
    $tests[] = array(
      'robin="batman"',
      'robin',
      'batman',
    );

    return $tests;
  }

  /**
   * @dataProvider dataForTestGetVarEvalCodeProvider
   */
  public function testGetVarEvalCode($expected, $var_name, $value) {
    $this->assertSame($expected, $this->obj->getVarEvalCode($var_name, $value));
  }

  public function testTypecastValue() {
    $this->assertSame('null', Configuration::typecast(NULL));
    $this->assertSame('true', Configuration::typecast(TRUE));
    $this->assertSame('false', Configuration::typecast(FALSE));
    $this->assertSame('lorem', Configuration::typecast('lorem'));
    $this->assertSame(17, Configuration::typecast(17));
    $this->assertSame('17', Configuration::typecast('17'));
  }

  /**
   * Provides data for testFlattenReturnsArray.
   */
  public function dataForTestFlattenReturnsArrayProvider() {
    $tests = array();

    $tests[] = array(
      [
        "# --keys\ndeclare -a config_keys=(\"options\")",
        "# --keys options\ndeclare -a config_keys_93da65a9fd0004d9477aeac024e08e15=(\"dry-run\")",
        "# --keys options___dry-run\ndeclare -a config_keys_b51d87c148f639270b032b3971fabcb5=(\"help\")",
        "# options___dry-run___help\nconfig_1e1ef452655e89de738b63ec4336b7c3=\"bla bla\"",
      ],
      [
        'options' => [
          'dry-run' => [
            'help' => 'bla bla',
          ],
        ],
      ],
    );


    $tests[] = array(
      [
        "# --keys\ndeclare -a config_keys=(\"version\")",
        "# version\nconfig_2af72f100c356273d46284f6fd1dfc08=\"1.0\"",
      ],
      ['version' => "1.0"],
    );

    $tests[] = array(
      [
        "# --keys\ndeclare -a config_keys=(\"title\")",
        "# title\nconfig_d5d3db1765287eef77d7927cc956f50a=\"Lorem Ipsum\"",
      ],
      ['title' => "Lorem Ipsum"],
    );
    $tests[] = array(
      [
        "# --keys\ndeclare -a config_keys=(\"list\")",
        "# --keys list\ndeclare -a config_keys_10ae9fc7d453b0dd525d0edf2ede7961=(\"alpha\" \"bravo\" \"charlie\" \"delta\" \"foxtrot\")",
        "# list___alpha\nconfig_c08565b5ffb30c3645ff167705c2f78f=true",
        "# list___bravo\nconfig_d878d1ff06b965d41b399d2fcfcc76ba=false",
        "# list___charlie\nconfig_9695da54e0130e4e5dfad78aa681398b=null",
        "# list___delta\nconfig_b655fdb359bdf50ad07b2e75c7c385fd=\"echo\"",
        "# list___foxtrot\nconfig_83e9d24cfeb9afc0d089532383e01deb=17",
      ],
      [
        'list' => [
          'alpha' => TRUE,
          'bravo' => FALSE,
          'charlie' => NULL,
          'delta' => 'echo',
          'foxtrot' => 17,
        ],
      ],
    );
    $tests[] = array(
      [
        "# --keys\ndeclare -a config_keys=(\"list\")",
        "# list\ndeclare -a config_10ae9fc7d453b0dd525d0edf2ede7961=(\"7\" \"10\" \"12\")",
        "# --keys list\ndeclare -a config_keys_10ae9fc7d453b0dd525d0edf2ede7961=(\"0\" \"1\" \"2\")",
        "# list___0\nconfig_6c50503444347625d456f645eb79ccc9=7",
        "# list___1\nconfig_7fad59262eb46665d66d5dae01d3acf6=10",
        "# list___2\nconfig_facfbcba32a22ebaf56dc6ff45d08b94=12",
      ],
      ['list' => [7, 10, 12]],
    );

    return $tests;
  }

  /**
   * @dataProvider dataForTestFlattenReturnsArrayProvider
   */
  public function testFlattenReturnsArray($expected_lines, $subject) {
    $actual = $this->obj->flatten($subject);
    $this->assertInternalType('array', $actual);
    $this->assertCount(count($expected_lines), $actual);
    foreach ($expected_lines as $expected) {
      $this->assertContains($expected, $actual);
    }
  }

}

