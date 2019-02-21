<?php

namespace AKlump\LoftLib\Code;

use AKlump\LoftLib\Testing\DatasetTestBase;

class DatasetBravoTest extends DatasetTestBase {

  /**
   * @expectedException Exception
   * @expectedExceptionMessage "foo" is not an accepted key in
   */
  public function testInvalidSchemaKeyDoesntThrowOnGet() {
    DatasetBravo::dataset(['foo' => 'bar'])
      ->withContext()
      ->throwFirstProblem()
      ->get();
  }

  public function testSendingNumericKeyDoesNotThrowBecauseOurClassAllowsIt() {
    $set = [
      'integer' => 5,
      0 => [],
    ];
    $result = DatasetBravo::dataset($set)
      ->validate()
      ->throwFirstProblem()
      ->get();
    $this->assertArrayHasKey(0, $result);
    $this->assertArrayHasKey('integer', $result);
  }

  /**
   * @expectedException Exception
   */
  public function testSendingNumericKeyThrows() {
    DatasetBravo::dataset([
      '#integer' => 5,
      0 => [],
    ])->validate()->throwFirstProblem();
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessageRegExp /^Missing required field\: integer$/
   */
  public function testThrowFirstProblemWithoutDataset() {
    $obj = DatasetBravo::dataset();
    $obj->throwFirstProblem();
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessageRegExp {"double"\:9\.4}
   */
  public function testWithContextThrowFirstProblemIncludesDatasetAsJson() {
    $obj = DatasetBravo::dataset([
      'double' => 9.4,
    ]);
    $obj->withContext()->throwFirstProblem();
  }

  public function testWithContextGetProblemsIncludesDatasetAsJson() {
    $obj = DatasetBravo::dataset([
      'double' => 9.4,
    ]);
    $problems = $obj->withContext()->getProblems();
    $problem = $problems['integer'][0];
    $this->assertStringStartsWith('Missing required field: integer in', $problem);

    // Do it again and see that context is not there.
    $problems = $obj->getProblems();
    $problem = $problems['integer'][0];
    $this->assertSame('Missing required field: integer', $problem);
  }

  public function testDefaultForObjectIsStdClass() {
    $obj = DatasetBravo::dataset();
    $this->assertEquals(new \stdClass, $obj->get()['object']);
  }

  /**
   * Provides data for testDefaults.
   *
   * Enter each key and it's default value.
   */
  public function DataForTestDefaultsProvider() {
    $tests = array();
    $tests[] = array('integer', 0);
    $tests[] = array('double', 0.0);
    $tests[] = array('float', 0.0);
    $tests[] = array('string', '');
    $tests[] = array('array', array());
    $tests[] = array('null', NULL);

    return $tests;
  }

  /**
   * Provides data for testInvalidFormatShowsProblems.
   *
   * Add some keys (string types) for with values that should not pass the
   * match().
   */
  public function DataForTestInvalidFormatShowsProblemsProvider() {
    $tests = array();

    return $tests;
  }

  /**
   * Provides data for testMissingKeyShowsProblem.
   *
   * List all the required keys that exist in $this->objArgs
   */
  public function DataForTestMissingKeyShowsProblemProvider() {
    $tests = array();
    $tests[] = array('integer');

    return $tests;
  }

  public function setUp() {
    $this->objArgs = [
      DatasetBravo::example()->get(),
    ];
    $this->createObj();
  }

  protected function createObj() {
    list ($def) = $this->objArgs;
    $this->obj = new DatasetBravo($def);
  }
}
