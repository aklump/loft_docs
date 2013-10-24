<?php
/**
 * @file
 * Unit tests for the ParserClass
 *
 * @ingroup loft_docs
 * @{
 */
namespace aklump\loft_docs;
require_once '../vendor/autoload.php';
require_once '../classes/Parser.php';
require_once '../classes/MediaWikiParser.php';
require_once '../classes/ParseAction.php';

class MediaWikiParserTest extends \PHPUnit_Framework_TestCase {

  public function testConstruct() {
    $p = new MediaWikiParser();
    $actions = $p->getActions();
    $this->assertGreaterThan(0, count($actions));
  }

  public function testHeadings() {
    for ($i = 1; $i <= 10; ++$i) {
      $tag = "h$i";
      $p = new MediaWikiParser("<$tag>Title</$tag>");
      $wiki = str_repeat('=', $i);
      $this->assertEquals("{$wiki}Title{$wiki}", $p->parse());
    }
  }

  public function testParse() {
    $subject = <<<EOD
<h1>Level One</h1>
<p><em>Italic text</em></p>
<p><strong>Bold text</strong></p>
<p><strong><em>Bold and Italic text</em></strong></p>
<h2>Level Two</h2>
<h6>Level Six</h6>
EOD;
    $control = <<<EOD
=Level One=
''Italic text''

'''Bold text'''

'''''Bold and Italic text'''''

==Level Two==
======Level Six======
EOD;

    $p = new MediaWikiParser($subject);
    $this->assertEquals($control, $p->parse());
  }
}
