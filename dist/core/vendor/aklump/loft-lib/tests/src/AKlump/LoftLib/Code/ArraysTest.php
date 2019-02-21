<?php

namespace AKlump\LoftLib\Code;

/**
 * Test the class Arrays.
 */
class ArraysTest extends \PHPUnit_Framework_TestCase {

  /**
   * @expectedException InvalidArgumentException
   */
  public function testInsertBeforeValueThrowsOnAssociateArray() {
    $subject = ['do' => 're'];
    Arrays::insertBeforeValue($subject, 're', 'ray');
  }

  public function testInsertBeforeValueAppendsWhenValueNotFound() {
    $subject = ['header', 'ipsum', 'footer'];
    $index = Arrays::insertBeforeValue($subject, 'pear', 'lorem');
    $this->assertSame(3, $index);
    $this->assertSame(['header', 'ipsum', 'footer', 'lorem'], $subject);
  }

  public function testInsertBeforeValueWorksAsExpected() {
    $subject = ['header', 'ipsum', 'footer'];
    $index = Arrays::insertBeforeValue($subject, 'ipsum', 'lorem');
    $this->assertSame(1, $index);
    $this->assertSame(['header', 'lorem', 'ipsum', 'footer'], $subject);
  }

  public function testSuffleWithKeysWorksAsExpected() {
    $original = $subject = [
      'do' => 'dough',
      're' => 'ray',
      'mi' => 'me',
      'fa' => 'far',
    ];
    $subject = Arrays::shuffleWithKeys($subject);
    $this->assertEmpty(array_diff_key($subject, $original));
    $this->assertEmpty(array_diff($subject, $original));
    $this->assertNotSame(json_encode($original), json_encode($subject));
  }

  /**
   * Assert replaceKey works as it should.
   */
  public function testReplaceKeyDoesWhatItShould() {
    $array = [
      'do' => 'one',
      're' => 'two',
      'mi' => 'three',
    ];
    $control = [
      'do' => 'one',
      'ra' => 'two',
      'mi' => 'three',
    ];
    $this->assertSame($control, Arrays::replaceKey($array, 're', 'ra'));
  }

  public function testInsertBeforeKeyOnEmptyArraySetsArrayToMatchInsert() {
    $subject = [];
    Arrays::insertBeforeKey($subject, 'do', ['re' => 2]);
    $this->assertSame(['re' => 2], $subject);
  }

  public function testInsertBeforeFirstKeyWorks() {
    $subject = ['re' => 2, 'mi' => 3];
    Arrays::insertBeforeKey($subject, 're', ['do' => 1]);
    $this->assertSame(['do', 're', 'mi'], array_keys($subject));
    $this->assertSame([1, 2, 3], array_values($subject));
  }

  public function testInsertAfterKeyOnEmptyArraySetsArrayToMatchInsert() {
    $subject = [];
    Arrays::insertAfterKey($subject, 'do', ['re' => 2]);
    $this->assertSame(['re' => 2], $subject);
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testInsertAfterKeyNumericSearchThrows() {
    $subject = [];
    Arrays::insertAfterKey($subject, '3', ['re' => 2]);
  }

  public function testInsertAfterKeyWorks() {
    $subject = ['do' => 1, 'mi' => 3];
    Arrays::insertAfterKey($subject, 'do', ['re' => 2]);
    $this->assertSame(['do', 're', 'mi'], array_keys($subject));
    $this->assertSame([1, 2, 3], array_values($subject));
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testInsertAfterValueThrowsOnAssociateArray() {
    $subject = ['do' => 'do'];
    Arrays::insertAfterValue($subject, 'do', 'mi');
  }

  public function testInsertAfterValueInsertsAfterSecondValue() {
    $subject = ['do', 're', 'fa'];
    $position = Arrays::insertAfterValue($subject, 're', 'mi');

    $this->assertSame(['do', 're', 'mi', 'fa'], $subject);
    $this->assertSame(2, $position);
  }

  public function testInsertAfterValueInsertsAfterFirstValue() {
    $subject = ['do', 'mi'];
    $position = Arrays::insertAfterValue($subject, 'do', 're');

    $this->assertSame(['do', 're', 'mi'], $subject);
    $this->assertSame(1, $position);
  }

  public function testInsertAfterValueOnEmptyArrayAppends() {
    $subject = [];
    $position = Arrays::insertAfterValue($subject, 're', 'do');

    $this->assertSame(['do'], $subject);
    $this->assertSame(0, $position);
  }

  public function testArrayReplacesTwoValuesCorrectly() {
    $subject = ['hello', 'there', 'hello'];
    $control = ['god dag', 'there', 'god dag'];
    $this->assertSame($control, Arrays::replaceValue($subject, 'hello', 'god dag'));
  }

  public function testInsertBeforLastKeyWorks() {
    $subject = ['do' => 1, 'mi' => 3];
    Arrays::insertBeforeKey($subject, 'mi', ['re' => 2]);
    $this->assertSame(['do', 're', 'mi'], array_keys($subject));
    $this->assertSame([1, 2, 3], array_values($subject));
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testInsertBeforeKeyNumericSearchThrows() {
    $subject = [];
    Arrays::insertBeforeKey($subject, '3', ['re' => 2]);
  }

  /**
   * Provides data for testFlattenAndExpandParents.
   */
  function DataForTestFlattenAndExpandParentsProvider() {
    $tests = array();
    $tests[] = array(array(), '');
    $tests[] = array(
      array('do', 're', 'mi'),
      'do[re][mi]',
    );

    return $tests;
  }

  /**
   * @dataProvider DataForTestFlattenAndExpandParentsProvider
   */
  public function testFlattenAndExpandParents($array, $string) {
    $this->assertSame($string, Arrays::flattenParents($array));
    $this->assertSame($array, Arrays::expandParents($string));
  }

  /**
   * Provides data for testFormFuzzyGet.
   */
  function DataForTestFormFuzzyGetProvider() {
    $tests = array();
    $tests[] = array(
      array(
        '_val[comp][0]' => 'alpha',
        '_val[comp][1]' => 'bravo',
        '_val[discuss]' => 'http://',
        '_val[todo][0]' => 'do',
        '_val[todo][1]' => 're',
      ),
      '_val[todo]',
      array(
        'do',
        're',
      ),
    );
    $tests[] = array(
      array(
        '_val[todo][0]' => 'do',
        '_val[todo][1]' => 're',
        '_val[todo][2]' => 'mi',
        '_val[todo][3]' => 'fa',
      ),
      '_val[todo]',
      array(
        'do',
        're',
        'mi',
        'fa',
      ),
    );

    return $tests;
  }

  /**
   * @dataProvider DataForTestFormFuzzyGetProvider
   */
  public function testFormFuzzyGet($a, $b, $control) {
    $this->assertSame($control, Arrays::formFuzzyGet($a, $b));
  }

  public function testFormFuzzyGetDefault() {
    $a = array(
      '_val[todo][0]' => 'do',
    );
    $b = '_val[style]';
    $this->assertNull(Arrays::formFuzzyGet($a, $b));
    $this->assertSame(array('default'), Arrays::formFuzzyGet($a, $b, array('default')));
  }

  public function testFormFuzzyGetBadKey() {
    $a = array(
      '_val[todo][0]' => 'do',
      '_val[todo][1]' => 're',
      '_val[todo][2]' => 'mi',
      '_val[todo][3]' => 'fa',
    );
    $b = '_val[style]';
    $control = NULL;
    $this->assertSame($control, Arrays::formFuzzyGet($a, $b));
  }

  /**
   * Provides data for testFormFuzzyIntersectKey.
   */
  function DataForTestFormFuzzyIntersectKeyProvider() {
    $tests = array();
    $tests[] = array(
      array(
        '_val[tags]' => TRUE,
      ),
      array(
        '_val[tags]' => 'do',
        'id' => 123,
      ),
      array(
        '_val[tags]' => TRUE,
      ),
    );
    $tests[] = array(
      array(
        '_val[tags]' => TRUE,
      ),
      array(
        '_val[tags][0][_val]' => 'do',
        '_val[tags][1][_val]' => 're',
        'id' => 123,
      ),
      array(),
    );
    $tests[] = array(
      array(
        '_val[tags][0][_val]' => 'do',
        '_val[tags][1][_val]' => 're',
        'id' => 123,
      ),
      array(
        '_val[tags]' => TRUE,
      ),
      array(
        '_val[tags][0][_val]' => 'do',
        '_val[tags][1][_val]' => 're',
      ),
    );

    return $tests;
  }

  /**
   * @dataProvider DataForTestFormFuzzyIntersectKeyProvider
   */
  public function testFormFuzzyIntersectKey($a, $b, $control) {
    $this->assertSame($control, Arrays::formFuzzyIntersectKey($a, $b));
  }

  public function testFormFuzzyDiffKey() {
    $keys = array(
      '_val[tags]' => TRUE,
    );
    $array = array(
      '_val[tags][0][_val]' => 'do',
      '_val[tags][1][_val]' => 're',
      'id' => 123,
    );
    $control = array(
      'id' => 123,
    );
    $this->assertSame($control, Arrays::formFuzzyDiffKey($array, $keys));
  }

  /**
   * Provides data for testFormExpand.
   */
  function DataForTestFormExpandProvider() {
    $tests = array();
    $tests[] = array(
      array('_val[todo][default]' => array()),
      array(
        '_val' => array(
          'todo' => array(
            'default' => array(),
          ),
        ),
      ),
    );
    $tests[] = array(
      array(
        '_val[comp]' => 'dir/file.png',
        '_val[comp][0][type]' => 'mobile',
      ),
      array(
        '_val' => array(
          'comp' => array(
            array('_val' => 'dir/file.png', 'type' => 'mobile'),
          ),
        ),
      ),
    );
    $tests[] = array(
      array(
        '_val[comp][0][_val]' => 'dir/file.png',
        '_val[comp][1][_val]' => 'dir/file2.png',
      ),
      array(
        '_val' => array(
          'comp' => array(
            array('_val' => 'dir/file.png'),
            array('_val' => 'dir/file2.png'),
          ),
        ),
      ),
    );
    $tests[] = array(
      array('id' => NULL),
      array('id' => NULL),
    );
    $tests[] = array(
      array('weight' => 0),
      array('weight' => 0),
    );
    $tests[] = array(
      array('_val[title]' => 'Top Gun'),
      array('_val' => array('title' => 'Top Gun')),
    );
    $tests[] = array(
      array(
        '_val[title]' => 'Top Gun',
        '_val[subtitle]' => 'Great Movie',
      ),
      array(
        '_val' => array(
          'subtitle' => 'Great Movie',
          'title' => 'Top Gun',
        ),
      ),
    );

    return $tests;
  }

  /**
   * @dataProvider DataForTestFormExpandProvider
   */
  public function testFormExpand($subject, $control) {
    $this->assertSame($control, Arrays::formExpand($subject, '_val'));
  }

  public function _testArrayMergeSmartMultiDeep() {
    $a = array(
      'person' => array(
        'name' => 'Bryan',
        'last' => 'Smith',
        'children' => array(
          'Matt',
          'Scott',
        ),
      ),
    );
    $b = array(
      'person' => array(
        'name' => 'Sonia',
        'last' => 'Jones',
        'children' => array(
          'Aaron',
          'Brian',
          'Justin',
        ),
      ),
    );
    $control = array(
      'person' => array(
        'name' => array(
          'Bryan',
          'Sonia',
        ),
        'last' => 'Smith',
        'children' => array(
          'Matt',
          'Scott',
          'Aaron',
          'Brian',
          'Justin',
        ),
      ),
    );
    $schema = array(
      'multiple' => array(
        'person[name]',
        'person[children]',
      ),
    );
    $this->assertSame($control, Arrays::mergeSmart($schema, $a, $b));
  }

  public function _testArrayMergeSmartMulti() {
    $a = array('name' => 'Aaron');
    $b = array('name' => 'Brian');
    $control = array('name' => array('Aaron', 'Brian'));
    $schema = array(
      'multiple' => array('name'),
    );
    $this->assertSame($control, Arrays::mergeSmart($schema, $a, $b));
  }

  public function _testArrayMergeSmart() {
    $a = array('name' => 'Aaron');
    $b = array('name' => 'Brian');
    $control = array('name' => 'Aaron');
    $this->assertSame($control, Arrays::mergeSmart(NULL, $a, $b));
  }

  public function testFormExportWithParents() {
    $test = array(
      array(
        0 => array(
          '_attr' => array(
            'date' => '2017-02-05',
          ),
          '_val' => 'do/re/mi',
        ),
        1 => array(
          '_attr' => array(
            'date' => '2017-01-05',
          ),
          '_val' => 'fa/so',
        ),
      ),
      array(
        '_val[comp][0][_attr][date]' => '2017-02-05',
        '_val[comp][0][_val]' => 'do/re/mi',
        '_val[comp][1][_attr][date]' => '2017-01-05',
        '_val[comp][1][_val]' => 'fa/so',
      ),
      '_val[comp]',
      array('_val', 'comp'),
    );
    list($array, $flat, $prefix, $prefixArray) = $test;
    $this->assertSame($flat, Arrays::formExport($array, $prefix));
    $this->assertSame($flat, Arrays::formExport($array, $prefixArray));
  }

  /**
   * Provides data for testFormExport.
   */
  function DataForTestFormExportProvider() {
    $tests = array();
    $tests[] = array(
      array(),
      array(),
    );
    $tests[] = array(
      array(
        '_val' => array(
          'todo' => array(
            'default' => array(),
          ),
        ),
      ),
      array('_val[todo][default]' => array()),
    );
    $tests[] = array(
      array(
        'weight' => 0,
      ),
      array(
        'weight' => 0,
      ),
    );
    $tests[] = array(
      array(
        '_val' => array(
          'comp' => array(
            0 => array(
              '_val' => 'sign.png',
              'type' => 'desktop',
            ),
          ),
          'title' => 'Great Things',
        ),
      ),
      array(
        '_val[comp][0][_val]' => 'sign.png',
        '_val[comp][0][type]' => 'desktop',
        '_val[title]' => 'Great Things',
      ),
    );
    $tests[] = array(
      array(
        '_val' => array(
          'comp' => array(
            0 => array(
              '_val' => 'sign.png',
              'type' => 'desktop',
            ),
          ),
        ),
      ),
      array(
        '_val[comp][0][_val]' => 'sign.png',
        '_val[comp][0][type]' => 'desktop',
      ),
    );
    $tests[] = array(
      array(
        '_val' => array(
          'tag' => array('do', 're', 'mi'),
        ),
      ),
      array(
        '_val[tag][0]' => 'do',
        '_val[tag][1]' => 're',
        '_val[tag][2]' => 'mi',
      ),
    );

    return $tests;
  }

  /**
   * @dataProvider DataForTestFormExportProvider
   */
  public function testFormExport($multi, $flat, $prefix = NULL) {
    $this->assertSame($flat, Arrays::formExport($multi, $prefix));
    $this->assertSame($multi, Arrays::formExpand($flat));
  }
}

