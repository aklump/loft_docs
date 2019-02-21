<?php

namespace AKlump\LoftLib\Code;

use AKlump\LoftLib\Testing\DatasetTestBase;

class DatasetAlphaTest extends DatasetTestBase {

  public function testGetDefaultReturnsMasterValueWhenAnyAliasUsed() {
    DatasetAlpha::getDefault('me');
    $this->assertSame('myself', DatasetAlpha::getDefault('me'));
    $this->assertSame('myself', DatasetAlpha::getDefault('mi'));
    $this->assertSame('myself', DatasetAlpha::getDefault('moi'));
  }

  public function testJsonIncludesDefaults() {
    $obj = new TestingToStringIncludesDefaults(['id' => 5]);
    $this->assertSame('{"id":5,"version":"1.2.5"}', strval($obj));
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testInvalidExampleIndexThrows() {
    $this->obj->example(99);
  }

  public function testCreateMethodInstantiates() {
    $obj = DatasetAlpha::create(['do' => 're']);
    $this->assertInstanceOf('AKlump\LoftLib\Code\DatasetAlpha', $obj);
  }

  public function testMutateReturnsNewObjectWithNewValueWhenValueIsNew() {
    $obj = DatasetAlpha::create(['do' => 're']);
    $obj2 = $obj->mutate('do', 'dough');
    $this->assertNotSame($obj, $obj2);
    $this->assertSame('dough', $obj2->do);
  }

  public function testMutateReturnsNewObjectWhenValueIsSame() {
    $obj = DatasetAlpha::create(['do' => 're']);
    $obj2 = $obj->mutate('do', 're');
    $this->assertNotSame($obj, $obj2);
  }

  /**
   * @expectedException Exception
   */
  public function testSendingNumericKeyThrows() {
    DatasetBravo::dataset([
      '#re' => 'sun',
      0 => [],
    ])->validate()->throwFirstProblem();
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testMagicGetOnBogusThrows() {
    $this->obj->bogus;
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testGetDefaultWithBogusKeyThrows() {
    DatasetAlpha::getDefault('bogus');
  }

  public function testGetterWithAliasReturnsValue() {
    $this->assertSame('mom', $this->obj->moi);
    $this->assertSame('mom', $this->obj->me);
    $this->assertSame('mom', $this->obj->mi);
  }

  public function testGetWithDefaultsReturnsMissingKeysWithDefaults() {
    $item = $this->obj->import([
      're' => 'moon',
      'do' => 'bread',
    ])->get();
    $this->assertNotEmpty($item['date']);
    unset($item['date']);
    $this->assertSame([
      'do' => 'bread',
      're' => 'moon',
      'mi' => 'myself',
      'pi' => 3.14,
      'fo' => '',
      'list' => [],
      'boolean' => FALSE,
    ], $item);
  }

  public function testExampleDoesNotUseAliasWhenAliasWasUsedInConstructorButGetNoAlias() {
    $this->assertArrayNotHasKey('me', DatasetAlpha::example()->getNoAlias());

    $masters = DatasetAlpha::example()->getNoAlias();
    $this->assertArrayHasKey('mi', $masters);

    $keys = ['do', 're', 'mi', 'pi', 'fo', 'list', 'date', 'boolean'];
    $this->assertSame($keys, array_keys($masters));
  }

  public function testGetMasterAliasReturnsCorrectly() {
    $this->assertSame('mi', $this->callAsPublic('getMasterAlias', 'mi'));
    $this->assertSame('mi', $this->callAsPublic('getMasterAlias', 'me'));
    $this->assertSame('mi', $this->callAsPublic('getMasterAlias', 'moi'));
  }

  public function testGetNotMasterAliasesReturnsCorrectly() {
    $this->assertSame([
      'me',
      'moi',
    ], $this->callAsPublic('getNotMasterAliases', 'mi'));
    $this->assertSame([
      'me',
      'moi',
    ], $this->callAsPublic('getNotMasterAliases', 'me'));
    $this->assertSame([
      'me',
      'moi',
    ], $this->callAsPublic('getNotMasterAliases', 'moi'));
  }

  public function testGetOtherAliasesReturnsCorrectly() {
    $this->assertSame([
      'me',
      'moi',
    ], $this->callAsPublic('getOtherAliases', 'mi'));
    $this->assertSame([
      'mi',
      'moi',
    ], $this->callAsPublic('getOtherAliases', 'me'));
    $this->assertSame([
      'mi',
      'me',
    ], $this->callAsPublic('getOtherAliases', 'moi'));
  }

  public function testGetAllAliasesReturnsCorrectly() {
    $this->assertSame([
      'mi',
      'me',
      'moi',
    ], $this->callAsPublic('getAllAliases', 'mi'));
    $this->assertSame([
      'mi',
      'me',
      'moi',
    ], $this->callAsPublic('getAllAliases', 'me'));
    $this->assertSame([
      'mi',
      'me',
      'moi',
    ], $this->callAsPublic('getAllAliases', 'moi'));
  }

  public function testGetNoAliasWithDefaultsReturnsMissingKeysWithDefaults() {
    $item = $this->obj->import([
      're' => 'moon',
      'do' => 'bread',
      'me' => 'Aaron',
    ])->getNoAlias();
    $this->assertNotEmpty($item['date']);
    unset($item['date']);
    $this->assertSame([
      'do' => 'bread',
      're' => 'moon',
      'mi' => 'Aaron',
      'pi' => 3.14,
      'fo' => '',
      'list' => [],
      'boolean' => FALSE,
    ], $item);
  }

  public function testExampleUsesAliasWhenAliasWasUsedInConstructor() {
    $this->assertArrayHasKey('me', DatasetAlpha::example()->get());
  }

  public function testStaticDatasetMethodReturnsInstanceWithData() {
    $control = $this->obj->get();
    $this->assertSame($control, DatasetAlpha::dataset($control)->get());
  }

  public function testGetDefaultsReturnsAsExpected() {
    $control = [
      'do' => 'deer',
      're' => 'sun',
      'mi' => 'myself',
      'pi' => 3.14,
      'fo' => '',
      'list' => [],
      'boolean' => FALSE,
    ];
    $defaults = DatasetAlpha::getDefaults();
    $this->assertNotEmpty($defaults['date']);
    unset($defaults['date']);
    $this->assertSame($control, $defaults);
  }

  public function testSchemaContainsAllExpectedKeys() {
    $control = [
      'do',
      're',
      'mi',
      'me',
      'moi',
      'pi',
      'fo',
      'list',
      'date',
      'boolean',
    ];
    $this->assertSame($control, array_keys($s = DatasetAlpha::getSchema()));

    $control = [
      'id',
      'default',
      'master',
      'is_alias',
      'aliases',
      'required',
      'mask',
      'types',
      'description',
    ];
    $this->assertSame($control, array_keys($s = reset($s)));

    $this->assertInternalType('string', $s['id']);

    $this->assertInternalType('string', $s['master']);
    $this->assertInternalType('boolean', $s['is_alias']);
    $this->assertInternalType('array', $s['aliases']);
    $this->assertInternalType('boolean', $s['required']);

    $this->assertInternalType('array', $s['types']);
    $this->assertInternalType('string', $s['description']);
  }

  public function testThrowItReturnsThisWhenNoProblems() {
    $this->createObj();
    $this->assertSame($this->obj, $this->obj->validate()->throwFirstProblem());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testThrowItThrowsWithProblem() {
    $this->objArgs[0]['pi'] = 'demo';
    $this->createObj();
    $this->obj->validate()->throwFirstProblem();
  }

  public function testValidateBasedOnWrongDataTypeFindsProblem() {
    $this->objArgs[0]['pi'] = 'demo';
    $this->createObj();
    $this->assertGreaterThan(0, count($this->obj->validate()->getProblems()));
  }

  public function testMarkdown() {
    $this->assertInternalType('string', $this->obj->getMarkdown());
  }

  public function testImportObject() {
    $json = json_encode($this->objArgs[0]);
    $subject = (object) $this->objArgs[0];
    $subject = strval($this->obj->import($subject));
    $this->assertSame($json, $subject);
  }

  public function testImportArray() {
    $json = json_encode($this->objArgs[0]);
    $subject = strval($this->obj->import($this->objArgs[0]));
    $this->assertSame($json, $subject);
  }

  public function testImportJson() {
    $json = json_encode($this->objArgs[0]);
    $subject = strval($this->obj->import($json));
    $this->assertSame($json, $subject);
  }


  /**
   * Provides data for testDefaults.
   *
   * Enter a test for each key and alias that has a default value.  If no
   * default values, test the first key for null.
   */
  public function DataForTestDefaultsProvider() {
    $tests = array();
    $tests[] = array('do', 'deer');
    $tests[] = array('re', 'sun');
    $tests[] = array('mi', 'myself');
    $tests[] = array('me', 'myself');
    $tests[] = array('pi', 3.14);

    return $tests;
  }

  /**
   * Provides data for testInvalidFormatShowsProblems.
   *
   * Add some keys with invalid values.
   */
  public function DataForTestInvalidFormatShowsProblemsProvider() {
    $tests = array();
    $tests[] = array('mi', 'you');

    return $tests;
  }

  /**
   * Provides data for testMissingKeyShowsProblem.
   *
   * List all the required keys that exist in $this->objArgs
   */
  public function DataForTestMissingKeyShowsProblemProvider() {
    $tests = array();
    $tests[] = array('re');

    return $tests;
  }

  public function setUp() {
    $this->objArgs = [
      DatasetAlpha::example()->get(),
    ];
    $this->createObj();
  }

  protected function createObj() {
    list ($def) = $this->objArgs;
    $this->obj = new DatasetAlpha($def);
  }
}

class TestingToStringIncludesDefaults extends Dataset {

  const JSON_SCHEMA = '{"$schema": "http://json-schema.org/draft-07/schema#","type":"object","required":["id"],"properties":{"id":{"type":"integer"},"version":{"type":"string","default":"1.2.5"}},"additionalProperties":false}';

}
