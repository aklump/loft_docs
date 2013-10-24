<?php
/**
 * @file
 * Unit tests for the ParseActionClass
 *
 * @ingroup loft_docs
 * @{
 */
namespace aklump\loft_docs;
require_once '../vendor/autoload.php';
require_once '../classes/Parser.php';
require_once '../classes/ParseAction.php';

class ParseActionTest extends \PHPUnit_Framework_TestCase {

  public function testHTMLTags() {
    $a = new HTMLTagParseAction('h1', '=');
    $control = '<h1>Some Title</h1>';
    $return = $a->parse($control);
    $this->assertEquals('=Some Title=', $control);
    $this->assertInstanceOf('aklump\loft_docs\ParseAction', $return);

    $a = new HTMLTagParseAction('h1', '=');
    $control = '<h1 id="page-title" class=\'red\'>Some Title</h1>';
    $a->parse($control);
    $this->assertEquals('=Some Title=', $control);

    $b = new HTMLTagParseAction('strong', "'''");
    $control = '<h1><strong>A Strong Title</strong></h1>';
    $a->parse($control);
    $b->parse($control);
    $this->assertEquals("='''A Strong Title'''=", $control);

    $a = new HTMLTagParseAction('h1', '<H1>', '</H1>');
    $control = '<h1>Some Title</h1>';
    $a->parse($control);
    $this->assertEquals('<H1>Some Title</H1>', $control);

    $a = new HTMLTagParseAction('ol', '@code', '@endcode');
    $control = '<ol id="some-list-id"><li>do</li><li>re</li></ol>';
    $a->parse($control);
    $this->assertEquals('@code<li>do</li><li>re</li>@endcode', $control);
  }

  public function testHRs() {
    $a = new HRParseAction('-', 4);
    $subject = 'Text Before<hr />Text After';
    $return = $a->parse($subject);
    $this->assertEquals("Text Before\n----\nText After", $subject);
    $this->assertInstanceOf('aklump\loft_docs\ParseAction', $return);

    $a = new HRParseAction('=====');
    $subject = 'Text Before<hr />Text After';
    $a->parse($subject);
    $this->assertEquals("Text Before\n=====\nText After", $subject);

    $a = new HRParseAction('-', 4);
    $subject = 'Text Before<hr/>Text After';
    $a->parse($subject);
    $this->assertEquals("Text Before\n----\nText After", $subject);

    $a = new HRParseAction('-', 4);
    $subject = 'Text Before<hr class="rule" />Text After';
    $a->parse($subject);
    $this->assertEquals("Text Before\n----\nText After", $subject);
  }

  public function testLists() {
    $subject = <<<EOD
<ul id="my-list">
  <li>do</li>
  <li class="odd">re</li>
</ul>
EOD;

    $p = new ListParseAction();
    $return = $p->parse($subject);
    $this->assertEquals("\n* do\n* re\n\n", $subject);
    $this->assertInstanceOf('aklump\loft_docs\ParseAction', $return);

    $p = new ListParseAction('# ', '* ');
    $subject = "<ol><li>do</li><li>re</li></ol>";
    $p->parse($subject);
    $this->assertEquals("\n# do\n# re\n\n", $subject);

    $p = new ListParseAction('# ', '* ');
    $subject = '<ol id="my-list"><li>do</li><li class="odd">re</li></ol>';
    $p->parse($subject);
    $this->assertEquals("\n# do\n# re\n\n", $subject);

    $p = new ListParseAction('# ', '* ');
    $subject = "<ul><li>do</li><li>re</li></ul>";
    $p->parse($subject);
    $this->assertEquals("\n* do\n* re\n\n", $subject);

    $p = new ListParseAction('# ', '* ');
    $subject = "<UL><LI>do</LI><LI>re</LI></UL>";
    $p->parse($subject);
    $this->assertEquals("\n* do\n* re\n\n", $subject);

    $p = new ListParseAction('$ ', '% ');
    $subject = "<UL><LI>do</LI><LI>re</LI></UL>";
    $p->parse($subject);
    $this->assertEquals("\n% do\n% re\n\n", $subject);

    $p = new ListParseAction('$');
    $subject = "<ol><li>do</li><li>re</li></ol>";
    $p->parse($subject);
    $this->assertEquals("\n\$do\n\$re\n\n", $subject);
  }
  public function testLinks() {
    $p = new LinkParseAction('[$1 $2]');
    $subject = 'click <a href="http://www.google.com" class="link">here</a> for google';
    $return = $p->parse($subject);
    $this->assertEquals('click [http://www.google.com here] for google', $subject);
    $this->assertInstanceOf('aklump\loft_docs\LinkParseAction', $return);

    $p = new LinkParseAction('[$1 $2]');
    $subject = 'CLICK <A HREF="HTTP://WWW.GOOGLE.COM" CLASS="LINK">HERE</A> FOR GOOGLE';
    $p->parse($subject);
    $this->assertEquals('CLICK [HTTP://WWW.GOOGLE.COM HERE] FOR GOOGLE', $subject);

    $p = new LinkParseAction('[$1 $2]');
    $subject = 'click <a alt="some link to click" href="http://www.google.com" class="link">on this link</a> for google';
    $p->parse($subject);
    $this->assertEquals('click [http://www.google.com on this link] for google', $subject);

    $p = new LinkParseAction('[$2]($1)');
    $subject = 'click <a alt="some link to click" href="http://www.google.com" class="link">on this link</a> for google';
    $p->parse($subject);
    $this->assertEquals('click [on this link](http://www.google.com) for google', $subject);

  }
}
