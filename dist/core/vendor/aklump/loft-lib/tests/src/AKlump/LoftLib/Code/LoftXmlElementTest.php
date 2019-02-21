<?php

namespace AKlump\LoftLib\Code;

use AKlump\LoftLib\Testing\PhpUnitTestCase;

/**
 * Defines testing class LoftXmlElementTest.
 */
class LoftXmlElementTest extends PhpUnitTestCase {

  /**
   * Provides data for testFromArrayAsArray.
   */
  function DataForTestFromArrayAsArrayProvider() {
    $tests = array();

    $tests[] = array(
      '<report><title>New Feature</title><config><status value="0">not started</status><status value="3">in development</status></config></report>',
      array(
        'report' => array(
          'title' => 'New Feature',
          'config' => array(
            'status' => array(
              0 => array(
                '_attr' => array('value' => 0),
                '_val' => 'not started',
              ),
              1 => array(
                '_attr' => array('value' => 3),
                '_val' => 'in development',
              ),
            ),
          ),
        ),
      ),
    );

    $tests[] = array(
      '<facet><chapter weight="5" color="blue"><title author="Klump, Aaron">Somewhere</title></chapter></facet>',
      array(
        'facet' => array(
          'chapter' => array(
            '_val' => array(
              'title' => array(
                '_val' => 'Somewhere',
                '_attr' => array(
                  'author' => 'Klump, Aaron',
                ),
              ),
            ),
            '_attr' => array(
              'weight' => 5,
              'color' => 'blue',
            ),
          ),
        ),
      ),
    );

    $tests[] = array(
      '<do><re><mi><fa>so</fa></mi></re></do>',
      array(
        'do' => array(
          're' => array(
            'mi' => array(
              'fa' => 'so',
            ),
          ),
        ),
      ),
    );

    $tests[] = array(
      '<facet weight="0" uuid="1598fade-b775-3ff8-a904-8724998bd9b4"><title>Darkness</title></facet>',
      array(
        'facet' => array(
          '_val' => array('title' => 'Darkness'),
          '_attr' => array(
            'weight' => 0,
            'uuid' => '1598fade-b775-3ff8-a904-8724998bd9b4',
          ),
        ),
      ),
    );

    $tests[] = array(
      '<report size="50"><title><son>Aaron</son></title><title color="red">two</title><title>three</title></report>',
      array(
        'report' => array(
          '_val' => array(
            'title' => array(
              0 => array(
                'son' => 'Aaron',
              ),
              1 => array(
                '_val' => 'two',
                '_attr' => array(
                  'color' => 'red',
                ),
              ),
              2 => 'three',
            ),
          ),
          '_attr' => array(
            'size' => 50,
          ),
        ),
      ),
    );
    $tests[] = array(
      '<report size="50"><title>one</title><title>two</title></report>',
      array(
        'report' => array(
          '_val' => array(
            'title' => array(
              0 => 'one',
              1 => 'two',
            ),
          ),
          '_attr' => array('size' => 50),
        ),
      ),
    );
    $tests[] = array(
      '<report size="50"><title>one</title><person>Matt</person></report>',
      array(
        'report' => array(
          '_val' => array(
            'title' => 'one',
            'person' => 'Matt',
          ),
          '_attr' => array('size' => 50),
        ),
      ),
    );
    $tests[] = array(
      '<title weight="10">Somewhere</title>',
      array(
        'title' => array(
          '_val' => 'Somewhere',
          '_attr' => array('weight' => 10),
        ),
      ),
    );

    $tests[] = array(
      '<title>Somewhere</title>',
      array('title' => 'Somewhere'),
    );
    $tests[] = array(
      '<report/>',
      array('report' => NULL),
    );

    $tests[] = array(
      '<report size="50"/>',
      array(
        'report' => array(
          '_attr' => array('size' => 50),
        ),
      ),
    );


    return $tests;
  }

  /**
   * @dataProvider DataForTestFromArrayAsArrayProvider
   */
  public function testFromArrayAsArray($xml, array $array) {
    $xml = trim($xml);
    $obj = LoftXmlElement::fromArray($array);
    $this->assertSame("<?xml version=\"1.0\"?>\n$xml\n", $obj->asXml());
    $this->assertEquals($array, $obj->asArray());
  }

  public function testStripsWhiteSpaceAsArray() {
    $xml = '<ship><captain>

James T. Kirk
</captain>
<number>123</number></ship>';
    $data = new LoftXmlElement($xml);
    $array = $data->asArray();
    $this->assertSame('James T. Kirk', $array['ship']['captain']);
    $this->assertSame(123, $array['ship']['number']);
  }

  public function testFromArrayMultiNonScalars() {
    $subject = array(
      'facet' => array(
        'tag' => array(
          array('_val' => 'alpha', '_attr' => array('weight' => 0)),
          array('_val' => 'bravo', '_attr' => array('weight' => 3)),
        ),
      ),
    );
    $this->assertSame('<?xml version="1.0"?>
<facet><tag weight="0">alpha</tag><tag weight="3">bravo</tag></facet>
', LoftXmlElement::fromArray($subject)->asXml());
  }

  public function testFromArrayMultiValues() {
    $subject = array(
      'facet' => array(
        'tag' => array(
          'do',
          're',
          'mi',
        ),
      ),
    );
    $this->assertSame('<?xml version="1.0"?>
<facet><tag>do</tag><tag>re</tag><tag>mi</tag></facet>
', LoftXmlElement::fromArray($subject)->asXml());
  }

  public function testFromArrayChildOnRoot() {
    $subject = array(
      'facet' => array(
        'title' => 'Chapter',
        'subtitle' => 'Life',
      ),
    );
    $this->assertSame('<?xml version="1.0"?>
<facet><title>Chapter</title><subtitle>Life</subtitle></facet>
', LoftXmlElement::fromArray($subject)->asXml());
  }

  public function testFromArrayAttributesOnRootWithValue() {
    $subject = array(
      'facet' => array(
        '_val' => array(
          'title' => 'Mr.',
        ),
        '_attr' => array(
          'weight' => 5,
          'uuid' => 1234,
        ),
      ),
    );
    $xml = LoftXmlElement::fromArray($subject);
    $this->assertSame('<?xml version="1.0"?>
<facet weight="5" uuid="1234"><title>Mr.</title></facet>
', $xml->asXml());
  }

  public function testFromArrayAttributesOnRootNullVal() {
    $subject = array(
      'facet' => array(
        '_attr' => array(
          'weight' => 5,
          'uuid' => '1234',
        ),
      ),
    );
    $xml = LoftXmlElement::fromArray($subject);
    $this->assertSame('<?xml version="1.0"?>
<facet weight="5" uuid="1234"/>
', $xml->asXml());
  }

  public function testFromArrayNoKeyNoAttributesOnRoot() {
    $subject = array(
      'facet' => array(
        'weight' => 5,
        'uuid' => '1234',
      ),
    );
    $xml = LoftXmlElement::fromArray($subject);
    $this->assertSame('<?xml version="1.0"?>
<facet><weight>5</weight><uuid>1234</uuid></facet>
', $xml->asXml());
  }

  public function testFromArrayNested() {
    $subject = array(
      'facet' => array(
        'chapter' => array(
          '_val' => array(
            'title' => array(
              '_val' => 'Somewhere',
              '_attr' => array(
                'author' => 'Klump, Aaron',
              ),
            ),
          ),
          '_attr' => array(
            'weight' => 5,
            'color' => 'blue',
          ),
        ),
      ),
    );
    $xml = LoftXmlElement::fromArray($subject);

    $this->assertSame('<?xml version="1.0"?>
<facet><chapter weight="5" color="blue"><title author="Klump, Aaron">Somewhere</title></chapter></facet>
', $xml->asXml());
  }

  public function testMultipleChildrenSameTagnameWithAttributes() {
    $xml = new LoftXmlElement('<facet/>');
    $xml->addChild('comp', 'alpha.png')->addAttribute('sort', 1);
    $xml->addChild('comp', 'bravo.png')->addAttribute('sort', 2);;
    $xml->addChild('comp', 'charlie.png')->addAttribute('sort', 3);;
    $xml->addChild('comp', 'delta.png')->addAttribute('sort', 4);;
    $control = array(
      'facet' => array(
        'comp' => array(
          array(
            '_val' => 'alpha.png',
            '_attr' => array('sort' => 1),
          ),
          array(
            '_val' => 'bravo.png',
            '_attr' => array('sort' => 2),
          ),
          array(
            '_val' => 'charlie.png',
            '_attr' => array('sort' => 3),
          ),
          array(
            '_val' => 'delta.png',
            '_attr' => array('sort' => 4),
          ),
        ),
      ),
    );
    $this->assertSame($control, $xml->asArray());
  }

  public function testMultipleChildrenSameTagname() {
    $xml = new LoftXmlElement('<facet/>');
    $xml->addChild('comp', 'alpha.png');
    $xml->addChild('comp', 'bravo.png');
    $xml->addChild('comp', 'charlie.png');
    $xml->addChild('comp', 'delta.png');
    $control = array(
      'facet' => array(
        'comp' => array(
          'alpha.png',
          'bravo.png',
          'charlie.png',
          'delta.png',
        ),
      ),
    );
    $this->assertSame($control, $xml->asArray());
  }

  public function testAsArrayAttributeOnRoot() {
    $xml = new LoftXmlElement('<facet/>');
    $xml->addChild('title', 'Darkness');
    $xml->addAttribute('weight', 0);
    $xml->addAttribute('uuid', '1598fade-b775-3ff8-a904-8724998bd9b4');
    $control = array(
      'facet' => array(
        '_val' => array('title' => 'Darkness'),
        '_attr' => array(
          'weight' => 0,
          'uuid' => '1598fade-b775-3ff8-a904-8724998bd9b4',
        ),
      ),
    );
    $this->assertSame($control, $xml->asArray());
  }

  /**
   * Provides data for testGetTemplateWithData.
   */
  function DataForTestGetTemplateWithDataProvider() {
    $tests = array();
    $tests[] = array(
      'comp',
      array(
        '_val' => 'http://comp.com',
        '_attr' => array(
          'type' => 'mobile',
          '#invalid' => 5,
          6 => 'numerickeysignored',
        ),
      ),
      function ($value, $xml) {
        $control = '<comp type="mobile">http://comp.com</comp>';
        $this->assertSame($control, $xml->asXML());
      },
    );
    $tests[] = array('title', 'My Title');
    $tests[] = array(
      'title',
      'My Title',
      function ($value, $xml) {
        $this->assertXmlEquals('My Title', $xml);
      },
    );
    $tests[] = array('status', 3);
    $tests[] = array('discuss', 'http://discuss.com');

    return $tests;
  }

  /**
   * @dataProvider DataForTestGetTemplateWithDataProvider
   */
  public function testGetTemplateWithData($key, $value, callable $control = NULL) {
    $control = $control ? $control : array($this, 'assertXMLEquals');
    $xml = new LoftXmlElement('<facet/>');
    $xml->addChildFromArray($key, $value);
    $this->assertXMLHasChild($key, $xml);
    $control($value, $xml->{$key});
  }

  public function testStripHeader() {
    $subject = '<?xml version="1.0"?><h1>Title</h1>';
    $this->assertSame('<h1>Title</h1>', LoftXmlElement::stripHeader($subject));
    $subject = "<?xml version=\"1.0\"?>\n<h1>Title</h1>";
    $this->assertSame('<h1>Title</h1>', LoftXmlElement::stripHeader($subject));
  }

  public function testLineBreaksStillDetectCData() {
    $subject = "What if the world embodied our highest potential? What would it look like? As the structures of modern society crumble, where do we find solutions that can help us build the future that serves us all? 

  This 25-minute Global Oneness Project film retrospective asks us to reflect on the state of the&hellip;";
    $original = $subject;
    $this->assertTrue(LoftXmlElement::wrapCData($subject));
    $this->assertSame("<![CDATA[{$original}]]>", $subject);
  }

  public function testStripCData() {
    $subject = "<![CDATA[<do>re</do>]]>";
    $this->assertSame('<do>re</do>', LoftXmlElement::stripCData($subject));
  }

  public function testaddCDataChild() {
    $obj = new LoftXmlElement('<test/>');
    $obj->addCDataChild('link', '<a href="https://www.apple.com">Apple</a>');
    $control = '<?xml version="1.0"?>
<test><link><![CDATA[<a href="https://www.apple.com">Apple</a>]]></link></test>
';
    $this->assertSame($control, $obj->asXml());
  }

  public function testWrapsLinkTag() {
    $value = '<a href="https://www.apple.com">Apple</a>';
    $this->assertTrue(LoftXmlElement::wrapCData($value));
    $this->assertSame('<![CDATA[<a href="https://www.apple.com">Apple</a>]]>', $value);
  }

  public function testAddChildAutoEntities() {
    $obj = new LoftXmlElement('<test/>');
    $obj::setConfig('autoEntities', TRUE);
    $obj->addChild('pair', 'tom & ruby');
    $obj->addChild('pair', 'tom < ruby');
    $obj->addChild('pair', 'tom > ruby');
    $this->assertSame('<?xml version="1.0"?>
<test><pair>tom &amp; ruby</pair><pair>tom &lt; ruby</pair><pair>tom &gt; ruby</pair></test>
', $obj->asXml());
  }

  public function testNoCDataOnTomltRuby() {
    $value = $control = 'tom < ruby';
    $this->assertFalse(LoftXmlElement::wrapCData($value));
    $this->assertSame($control, $value);
  }

  public function testWrapCDataNoForce() {
    $value = '&apos;';
    $this->assertFalse(LoftXmlElement::wrapCData($value));
    $this->assertSame('&apos;', $value);

    $value = '&quot;';
    $this->assertFalse(LoftXmlElement::wrapCData($value));
    $this->assertSame('&quot;', $value);

    $value = '&amp;';
    $this->assertFalse(LoftXmlElement::wrapCData($value));
    $this->assertSame('&amp;', $value);

    $value = '&lt;';
    $this->assertFalse(LoftXmlElement::wrapCData($value));
    $this->assertSame('&lt;', $value);

    $value = '&gt;';
    $this->assertFalse(LoftXmlElement::wrapCData($value));
    $this->assertSame('&gt;', $value);
  }

  public function testWrapCDataWithEntity() {
    $value = '&apos;He said there is&hellip;&apos;';
    $this->assertTrue(LoftXmlElement::wrapCData($value));
    $this->assertSame('<![CDATA[&apos;He said there is&hellip;&apos;]]>', $value);

    $value = '"He said there is&hellip;"';
    $this->assertTrue(LoftXmlElement::wrapCData($value));
    $this->assertSame('<![CDATA["He said there is&hellip;"]]>', $value);
  }

  public function testDoNotWrapEmptyUnlessForce() {
    $value = '';
    $this->assertFalse(LoftXmlElement::wrapCData($value));
    $this->assertSame('', $value);

    $value = '';
    $this->assertTrue(LoftXmlElement::wrapCData($value, TRUE));
    $this->assertSame('<![CDATA[]]>', $value);
  }

  public function testDoNotDoubleWrapCDataUnlessForced() {
    $value = '<![CDATA[<strong>]]>';
    $this->assertFalse(LoftXmlElement::wrapCData($value));
    $this->assertSame('<![CDATA[<strong>]]>', $value);

    $value = '<![CDATA[<strong>]]>';
    $this->assertTrue(LoftXmlElement::wrapCData($value, TRUE));
    $this->assertSame('<![CDATA[<![CDATA[<strong>]]>]]>', $value);
  }

  public function testWrapCDataForce() {
    $value = '';
    $this->assertTrue(LoftXmlElement::wrapCData($value, TRUE));
    $this->assertSame('<![CDATA[]]>', $value);

    $value = '<strong>';
    $this->assertTrue(LoftXmlElement::wrapCData($value, TRUE));
    $this->assertSame('<![CDATA[<strong>]]>', $value);

    $value = 'stuffed';
    $this->assertTrue(LoftXmlElement::wrapCData($value, TRUE));
    $this->assertSame('<![CDATA[stuffed]]>', $value);
  }

  public function testEscapeXMLSpecialCharsChange() {
    $value = 'tom \' ruby';
    $this->assertTrue(LoftXmlElement::xmlChars($value));
    $this->assertSame("tom &apos; ruby", $value);

    $value = "tom ' ruby";
    $this->assertTrue(LoftXmlElement::xmlChars($value));
    $this->assertSame('tom &apos; ruby', $value);

    $value = 'tom " ruby';
    $this->assertTrue(LoftXmlElement::xmlChars($value));
    $this->assertSame('tom &quot; ruby', $value);

    $value = 'tom & ruby';
    $this->assertTrue(LoftXmlElement::xmlChars($value));
    $this->assertSame('tom &amp; ruby', $value);

    $value = 'tom < ruby';
    $this->assertTrue(LoftXmlElement::xmlChars($value));
    $this->assertSame('tom &lt; ruby', $value);

    $value = 'tom > ruby';
    $this->assertTrue(LoftXmlElement::xmlChars($value));
    $this->assertSame('tom &gt; ruby', $value);
  }

  public function testEscapeXMLSpecialCharsNoChange() {
    $value = $control = "tom";
    $this->assertFalse(LoftXmlElement::xmlChars($value));
    $this->assertSame($control, $value);

    $value = "tom &#039; ruby";
    $control = "tom &apos; ruby";
    $this->assertFalse(LoftXmlElement::xmlChars($value));
    $this->assertSame($control, $value);

    $value = $control = "tom &apos; ruby";
    $this->assertFalse(LoftXmlElement::xmlChars($value));
    $this->assertSame($control, $value);

    $value = $control = 'tom &quot; ruby';
    $this->assertFalse(LoftXmlElement::xmlChars($value));
    $this->assertSame($control, $value);

    $value = $control = 'tom &amp; ruby';
    $this->assertFalse(LoftXmlElement::xmlChars($value));
    $this->assertSame($control, $value);

    $value = $control = 'tom &lt; ruby';
    $this->assertFalse(LoftXmlElement::xmlChars($value));
    $this->assertSame($control, $value);

    $value = $control = 'tom &gt; ruby';
    $this->assertFalse(LoftXmlElement::xmlChars($value));
    $this->assertSame($control, $value);
  }
}
