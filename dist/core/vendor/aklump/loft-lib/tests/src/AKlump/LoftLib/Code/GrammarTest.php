<?php
/**
 * @file
 * PHPUnit tests for the Grammar class
 */

namespace AKlump\LoftLib\Code;

use PHPUnit\Framework\TestCase;

class GrammarTest extends TestCase {

  /**
   * Provides data for testTitleCaseWorksOnRealWorldExamples.
   */
  public function DataForTestTitleWorksOnRealWorldExamplesProvider() {
    $tests = array();
    $tests[] = array(
      'Check Out My MySQL Page',
      'check out my MySQL PAGE',
      ['MySQL'],
    );
    $tests[] = array(
      'Dynamic Blog Page Automatically Pulls in Three Different Social Feeds, on Schedule',
      'DYNAMIC BLOG PAGE AUTOMATICALLY PULLS IN THREE DIFFERENT SOCIAL FEEDS, ON SCHEDULE',
      [],
    );
    $tests[] = array(
      'Dynamic Blog Page Automatically Pulls in Three Different Social Feeds, on Schedule',
      'dynamic blog page automatically pulls in three different social feeds, on schedule',
      [],
    );

    return $tests;
  }

  /**
   * @dataProvider DataForTestTitleWorksOnRealWorldExamplesProvider
   */
  public function testTitleCaseWorksOnRealWorldExamples($control, $subject, $ignore) {
    $this->assertSame($control, Grammar::titleCase($subject, $ignore));
  }

  public function testTitleCaseDoesNotCapitalizeCertainShortwords() {
    $noCapitals = explode(' ', 'a an the at by for in of on to up and as but or nor');
    foreach ($noCapitals as $word) {
      $this->assertSame(ucfirst($word), Grammar::titleCase($word));
      $this->assertSame('Lorem ' . ucfirst($word), Grammar::titleCase('lorem ' . $word));
      $this->assertSame("Lorem $word Ipsum", Grammar::titleCase("lorem $word ipsum"));
    }
  }

  /**
   * Provides data for testIsConsonantReturnsTrueForAllConsonants.
   */
  public function DataForTestIsVowelReturnsTrueForAllVowelsProvider() {
    return array_map(function ($item) {
      return [$item];
    }, str_split('aeiou'));
  }

  /**
   * @dataProvider DataForTestIsVowelReturnsTrueForAllVowelsProvider
   */
  public function testIsVowelReturnsTrueForAllVowels($subject) {
    $this->assertTrue(Grammar::isVowel($subject));
  }

  /**
   * Provides data for testIsConsonantReturnsTrueForAllConsonants.
   */
  public function DataForTestIsConsonantReturnsTrueForAllConsonantsProvider() {
    return array_map(function ($item) {
      return [$item];
    }, str_split('bcdfghjklmnpqrstvwxy'));
  }

  /**
   * @dataProvider DataForTestIsConsonantReturnsTrueForAllConsonantsProvider
   */
  public function testIsConsonantReturnsTrueForAllConsonants($subject) {
    $this->assertTrue(Grammar::isConsonant($subject));
  }

  /**
   * Provides data for testPresentParticiple.
   */
  function DataForTestPresentParticipleProvider() {
    $tests = array();
    $tests[] = array('Start', 'Starting');
    $tests[] = array('unpublish', 'unpublishing');
    $tests[] = array('publish', 'publishing');
    $tests[] = array('sign up', 'signing up');
    $tests[] = array('Create', 'Creating');
    $tests[] = array('create', 'creating');
    $tests[] = array('Delete', 'Deleting');
    $tests[] = array('delete', 'deleting');
    $tests[] = array('save', 'saving');
    $tests[] = array('submit', 'submitting');
    $tests[] = array('click', 'clicking');
    $tests[] = array('clear', 'clearing');
    $tests[] = array('Log In', 'Logging In');

    return $tests;
  }

  /**
   * @dataProvider DataForTestPresentParticipleProvider
   */
  public function testPresentParticiple($base, $control) {
    $this->assertSame($control, Grammar::presentParticiple($base));
  }

  /**
   * Provides data for testPastTense.
   */
  function DataForTestPastTenseProvider() {
    $tests = array();
    $tests[] = array('remove', 'removed');
    $tests[] = array('save', 'saved');
    $tests[] = array('submit', 'submitted');
    $tests[] = array('update', 'updated');
    $tests[] = array('click', 'clicked');
    $tests[] = array('clear', 'cleared');
    $tests[] = array('Log In', 'Logged In');

    return $tests;
  }

  /**
   * @dataProvider DataForTestPastTenseProvider
   */
  public function testPastTense($base, $control) {
    $this->assertSame($control, Grammar::pastTense($base));
  }

  /**
   * @dataProvider toPluralProvider
   */
  public function testToSingular($control, $subject) {
    $this->assertSame($control, Grammar::singular($subject));
  }

  /**
   * Provides data for testToPlural and testToSingular.
   */
  function toPluralProvider() {
    $tests = array();
    $tests[] = array(
      'seed',
      'seeds',
    );
    $tests[] = array(
      'man',
      'men',
    );
    $tests[] = array(
      'thief',
      'thieves',
    );
    $tests[] = array(
      'roof',
      'roofs',
    );
    $tests[] = array(
      'boy',
      'boys',
    );
    $tests[] = array(
      'city',
      'cities',
    );
    $tests[] = array(
      'car',
      'cars',
    );
    $tests[] = array(
      'car',
      'cars',
    );
    $tests[] = array(
      'cassette',
      'cassettes',
    );
    $tests[] = array(
      'lamp',
      'lamps',
    );
    $tests[] = array(
      'hat',
      'hats',
    );
    $tests[] = array(
      'cup',
      'cups',
    );

    return $tests;
  }

  /**
   * @dataProvider toPluralProvider
   */
  public function testToPlural($subject, $control) {
    $this->assertSame($control, Grammar::plural($subject));
  }

  /**
   * Provides data for testIsPlural.
   */
  function isPluralProvider() {
    $tests = array();
    $tests[] = array(
      'cars',
      TRUE,
    );
    $tests[] = array(
      'car',
      FALSE,
    );
    $tests[] = array(
      'man',
      FALSE,
    );
    $tests[] = array(
      'men',
      TRUE,
    );
    $tests[] = array(
      'thieves',
      TRUE,
    );

    return $tests;
  }

  /**
   * @dataProvider isPluralProvider
   */
  public function testIsPlural($subject, $control) {
    $this->assertSame($control, Grammar::isPlural($subject));
  }

  public function testEndsWith() {
    $this->assertTrue(Grammar::endsWith('apple', Grammar::VOWEL));
    $this->assertFalse(Grammar::endsWith('apple', Grammar::CONSONANT));
    $this->assertTrue(Grammar::endsWith('tent', Grammar::CONSONANT));
    $this->assertFalse(Grammar::endsWith('tent', Grammar::VOWEL));
  }

  public function testConsonants() {
    $consonants = Grammar::consonants();
    $this->assertSame('z', $consonants[122]);
    $this->assertArrayNotHasKey(97, $consonants);
  }

  public function testVowels() {
    $vowels = Grammar::vowels();
    $this->assertSame('a', $vowels[97]);
    $this->assertArrayNotHasKey(122, $vowels);
  }

  public function testLetters() {
    $letters = Grammar::letters();
    $this->assertSame('a', $letters[97]);
    $this->assertSame('z', $letters[122]);
  }

  public function testLettersByType() {
    $obj = new Grammar;
    $reflector = new \ReflectionClass(get_class($obj));
    $method = $reflector->getMethod('lettersByType');
    $method->setAccessible('public');
    $result = $method->invokeArgs($obj, array('other'));
    $this->assertSame(array(), $result);
  }
}
