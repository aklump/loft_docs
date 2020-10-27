<?php

namespace AKlump\LoftLib\Bash;

use PHPUnit\Framework\TestCase;

class LegacyConfigurationTest extends TestCase {

  public function setUp() {
    $this->dependencies = ['config', '___'];
    $this->createObj();
  }

  protected function createObj() {
    list ($prefix, $sep) = $this->dependencies;
    $this->obj = new LegacyConfiguration($prefix, $sep);
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
    $this->assertSame('null', LegacyConfiguration::typecast(NULL));
    $this->assertSame('true', LegacyConfiguration::typecast(TRUE));
    $this->assertSame('false', LegacyConfiguration::typecast(FALSE));
    $this->assertSame('lorem', LegacyConfiguration::typecast('lorem'));
    $this->assertSame(17, LegacyConfiguration::typecast(17));
    $this->assertSame('17', LegacyConfiguration::typecast('17'));
  }

  /**
   * Provides data for testFlattenReturnsArray.
   */
  public function dataForTestFlattenReturnsArrayProvider() {
    $tests = array();

    $tests[] = array(
      [
        'declare -a config_keys=("options")',
        'declare -a config_keys___options=("dry-run")',
        'declare -a config_keys___options___dry_run=("help")',
        'config___options___dry_run___help="bla bla"',
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
        'declare -a config_keys=("images")',
        'declare -a config_keys___images=("types")',
        'declare -a config_keys___images___types=("bitmap")',
        'declare -a config___images___types___bitmap=("jpg" "png" "gif")',
        'declare -a config_keys___images___types___bitmap=("0" "1" "2")',
        'config___images___types___bitmap___0="jpg"',
        'config___images___types___bitmap___1="png"',
        'config___images___types___bitmap___2="gif"',
      ],
      [
        'images' => [
          'types' => [
            'bitmap' => [
              "jpg",
              "png",
              "gif",
            ],
          ],
        ],
      ],
    );

    $tests[] = array(
      [
        'declare -a config_keys=("version")',
        'config___version="1.0"',
      ],
      ['version' => "1.0"],
    );

    $tests[] = array(
      [
        'declare -a config_keys=("title")',
        'config___title="Lorem Ipsum"',
      ],
      ['title' => "Lorem Ipsum"],
    );
    $tests[] = array(
      [
        'declare -a config_keys=("list")',
        'declare -a config_keys___list=("alpha" "bravo" "charlie" "delta" "foxtrot")',
        'config___list___alpha=true',
        'config___list___bravo=false',
        'config___list___charlie=null',
        'config___list___delta="echo"',
        'config___list___foxtrot=17',
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
        'declare -a config_keys=("list")',
        'declare -a config___list=("7" "10" "12")',
        'declare -a config_keys___list=("0" "1" "2")',
        'config___list___0=7',
        'config___list___1=10',
        'config___list___2=12',
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

