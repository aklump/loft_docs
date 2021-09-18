<?php

namespace AKlump\LoftLib\Code;

use AKlump\LoftLib\Testing\PhpUnitTestCase;

class ShortCodesTest extends PhpUnitTestCase {

  public function testShortcodesGetElementsWorkWithNBPS() {
    $base = 'lorem [foo&nbsp;id="1"]ipsum[/foo] dolar [bar&nbsp;id="5"]';
    $elements = ShortCodes::getElements($base);
    $this->assertCount(2, $elements);
    $this->assertSame('foo', $elements[0]['name']);
    $this->assertSame(1, $elements[0]['attributes']['id']);
    $this->assertSame('bar', $elements[1]['name']);
    $this->assertSame(5, $elements[1]['attributes']['id']);
  }

  public function testShortcodesInflateWorkWithNBPS() {
    $base = 'lorem [foo&nbsp;id="1"]ipsum[/foo] dolar [bar&nbsp;id="5"]';
    $inflated = ShortCodes::inflate($base, [
      'foo' => function ($html, $attributes) {
        return 'foo.' . $html . '.foo.' . $attributes['id'];
      },
      'bar' => function ($html, $attributes) {
        return 'bar.' . $attributes['id'];
      },
    ]);
    $this->assertSame('lorem foo.ipsum.foo.1 dolar bar.5', $inflated);
  }

  public function testAbleToGetSomeElementsIfAnotherFailsAttributeParsing() {
    $base = 'lorem [foo id="1"]ipsum[/foo] dolar [bar] sit [ãka wakã] amet';
    $elements = ShortCodes::getElements($base);

    $this->assertCount(2, $elements);
    $this->assertSame('foo', $elements[0]['name']);
    $this->assertSame(1, $elements[0]['attributes']['id']);

    $this->assertSame('bar', $elements[1]['name']);
    $this->assertEmpty($elements[1]['attributes']);
  }

  public function testAttributesCanHandleNBSPCharacter() {
    $base = 'indigenous[see_footnote id="1"] peoples,';
    $elements = ShortCodes::getElements($base);
    $this->assertCount(1, $elements);
    $this->assertSame('see_footnote', $elements[0]['name']);
    $this->assertSame(1, $elements[0]['attributes']['id']);
  }

  public function testPrepareCallsInTheOrderOfCallbacksNotPresentation() {
    $prepare_callbacks = [
      'bravo' => function ($base) {
        return $base . '.bravo';
      },
      'alpha' => function ($base) {
        return $base . '.alpha';
      },
    ];
    $this->assertSame('[alpha][bravo].bravo.alpha', ShortCodes::prepare('[alpha][bravo]', $prepare_callbacks));
  }

  public function testPrepareOnlyCallsWhenShortCodePresent() {
    $prepare_callbacks = [
      'caption' => function ($base) {
        return trim($base);
      },
    ];
    $this->assertSame('', ShortCodes::prepare('', $prepare_callbacks));
    $this->assertSame('[caption]', ShortCodes::prepare(' [caption] ', $prepare_callbacks));
  }

  /**
   * The WordPress API states "The shortcode parser uses a single pass on the
   * post content. This means that if the $content parameter of a shortcode
   * handler contains another shortcode, it won't be parsed:".  This test shows
   * that this class does not suffer from this limitation.
   */
  public function testWordPressDoublePassExample() {
    $shortcode = '[caption]Caption: [myshortcode][/caption]';
    $inflated = '<span class="caption">Caption: foo</span>';
    $this->assertSame($inflated, ShortCodes::inflate($shortcode, [
      'caption' => function ($inner_html) {
        return '<span class="caption">' . $inner_html . '</span>';
      },
      'myshortcode' => 'foo',
    ]));
  }

  /**
   * Provides data for testWordPressExamplesPass.
   */
  public function dataForTestWordPressExamplesPassProvider() {
    $tests = array();
    $tests[] = array(
      '[pull_quote margin_top="7"]',
      'pull_quote',
      ['margin_top' => 7],
      '',
      'foo',
    );
    $tests[] = array(
      '[pull_quote margin-top="7"]',
      'pull_quote',
      ['margin-top' => 7],
      '',
      'foo',
    );
    $tests[] = array(
      '[caption]Caption: [myshortcode][/caption]',
      'caption',
      [],
      'Caption: [myshortcode]',
      '<span class="caption">Caption: [myshortcode]</span>',
    );
    $tests[] = array(
      '[caption]<a href="http://example.com/">My Caption</a>[/caption]',
      'caption',
      [],
      '<a href="http://example.com/">My Caption</a>',
      '<span class="caption"><a href="http://example.com/">My Caption</a></span>',
    );
    $tests[] = array(
      '[myshortcode]content[/myshortcode]',
      'myshortcode',
      [],
      'content',
      '<h1>content</h1>',
    );
    $tests[] = array(
      '[bartag foo="foo-value"]',
      'bartag',
      ['foo' => 'foo-value'],
      '',
      '<div class="bartag">bartag</div>',
    );
    $tests[] = array(
      '[foobar]',
      'foobar',
      [],
      '',
      '<div class="foobar">FooBar</div>',
    );
    $tests[] = array(
      '[gallery id="123" size="medium"]',
      'gallery',
      ['id' => 123, 'size' => 'medium'],
      '',
      '<div class="gallery">lorem</div>',
    );

    return $tests;
  }

  /**
   * @dataProvider dataForTestWordPressExamplesPassProvider
   */
  public function testWordPressExamplesPass($shortcode, $name, $attributes, $inner, $inflated) {
    $elements = array_first(ShortCodes::getElements('lorem ipsum ' . $shortcode . ' dolar sit'));
    $this->assertSame($name, $elements['name']);
    $this->assertSame($inner, $elements['inner_html']);
    $this->assertSame($attributes, $elements['attributes']);
    $this->assertSame($inflated, ShortCodes::inflate($shortcode, [
      $name => function ($passed_inner, $passed_attributes) use ($inflated, $attributes) {
        $this->assertSame($attributes, (array) $passed_attributes);

        return $inflated;
      },
    ]));
  }

  public function testGetElementsWorksAsExpected() {
    $control = [
      0 => [
        'name' => 'anchor',
        'inner_html' => '',
        'attributes' => [
          'layout' => 'right',
          'color' => 'red',
        ],
      ],
      1 => [
        'name' => 'person',
        'inner_html' => 'Aaron',
        'attributes' => [
          'type' => 'leader',
          'age' => 44,
        ],
      ],
    ];
    $subject = 'Fusce vel sapien quis orci feugiat accumsan vel sit amet massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nunc varius turpis vel placerat eleifend. Vivamus tempus enim quam, sit amet porta libero efficitur ac. Maecenas ultricies, felis id vulputate consectetur, [anchor layout="right" color="red"]ligula ligula tempor augue, et feugiat sapien ante sit amet dui. Morbi ullamcorper justo nec purus cursus ullamcorper. Sed semper dictum tellus, vel varius metus pellentesque eu. Ut interdum tristique finibus. In pharetra nibh a malesuada dignissim. Etiam a interdum orci. Maecenas ultricies porttitor [person type="leader" age="44"]Aaron[/person]neque. Quisque sit amet tincidunt nulla, ut aliquam mauris. Pellentesque ut efficitur eros. Aenean vestibulum aliquet odio, quis pellentesque mauris congue id. Aenean vitae turpis id sapien sollicitudin blandit.';

    $this->assertSame($control, ShortCodes::getElements($subject));

    // Call a second time to ensure the static cache works.
    $this->assertSame($control, ShortCodes::getElements($subject));
  }

  public function testReplaceTagsWorksWhenComponentIsObjectAndCanBeCastToString() {
    $this->assertSame('Here is a nice juicy pie to eat.', ShortCodes::inflate('Here is [apple] to eat.', [
      'apple' => new ShortcodesCastableString(),
    ]));
  }

  public function testGetElementsOnEmptyStringReturnsEmptyArray() {
    $this->assertSame([], ShortCodes::getElements(''));
  }

  public function testGetElementsWorksWithoutAttributes() {
    $control = [
      0 => [
        'name' => 'heading',
        'inner_html' => 'Project Summary',
        'attributes' => [],
      ],
    ];
    $subject = '<p>[heading]Project Summary[/heading]</p>';

    $this->assertSame($control, ShortCodes::getElements($subject));
  }

  /**
   * Provides data for testReplaceTagsWorks.
   */
  public function dataForTestReplaceTagsWorksProvider() {
    $tests = array();

    // Self closing inside of of nested HTML.
    $tests[] = array(
      '<div><span class="bold">Here is [food] to eat.</span></div>',
      '<div><span class="bold">Here is dinner to eat.</span></div>',
      [
        'food' => function () {
          return 'dinner';
        },
      ],
    );

    // Non self-closing inside of nexted HTML.
    $tests[] = array(
      "<p>[heading]Showcasing Student Work[/heading]<br />
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent ac blandit risus. Mauris tempor a lacus a placerat. Vivamus viverra dapibus metus non finibus. Nulla ultricies est nulla, eget efficitur nibh viverra non. Sed sed est viverra nunc malesuada venenatis vitae at tellus. Suspendisse potenti. Morbi non blandit elit, sit amet consectetur mi.</p>
<p>[subheading]Gallery Walk[/subheading]<br />
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent ac blandit risus. Mauris tempor a lacus a placerat. Vivamus viverra dapibus metus non finibus. Nulla ultricies est nulla, eget efficitur nibh viverra non. Sed sed est viverra nunc malesuada venenatis vitae at tellus. Suspendisse potenti. Morbi non blandit elit, sit amet consectetur mi.</p>
<p>[subheading]Social Media[/subheading]<br />
Students can post their photographs and captions on <a href=\"http://develop.acme.loft/\">Twitter</a> or <a href=\"http://develop.acme.loft/\">Instagram</a> with the <strong>#RememberEarth</strong>. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent ac blandit risus. Mauris tempor a lacus a placerat. Vivamus viverra dapibus metus non finibus. Nulla ultricies est nulla, eget efficitur nibh viverra non. Sed sed est viverra nunc malesuada venenatis vitae at tellus. Suspendisse potenti. Morbi non blandit elit, sit amet consectetur mi.)</p>
<p>[subheading]Acme Website[/subheading]<br />
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent ac blandit risus. Mauris tempor a lacus a placerat. Vivamus viverra dapibus metus non finibus. Nulla ultricies est nulla, eget efficitur nibh viverra non. Sed sed est viverra nunc malesuada venenatis vitae at tellus. Suspendisse potenti. Morbi non blandit elit, sit amet consectetur mi., please contact us: [site_email]</p>
",
      "<p><h2>Showcasing Student Work</h2><br />
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent ac blandit risus. Mauris tempor a lacus a placerat. Vivamus viverra dapibus metus non finibus. Nulla ultricies est nulla, eget efficitur nibh viverra non. Sed sed est viverra nunc malesuada venenatis vitae at tellus. Suspendisse potenti. Morbi non blandit elit, sit amet consectetur mi.</p>
<p>[subheading]Gallery Walk[/subheading]<br />
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent ac blandit risus. Mauris tempor a lacus a placerat. Vivamus viverra dapibus metus non finibus. Nulla ultricies est nulla, eget efficitur nibh viverra non. Sed sed est viverra nunc malesuada venenatis vitae at tellus. Suspendisse potenti. Morbi non blandit elit, sit amet consectetur mi.</p>
<p>[subheading]Social Media[/subheading]<br />
Students can post their photographs and captions on <a href=\"http://develop.acme.loft/\">Twitter</a> or <a href=\"http://develop.acme.loft/\">Instagram</a> with the <strong>#RememberEarth</strong>. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent ac blandit risus. Mauris tempor a lacus a placerat. Vivamus viverra dapibus metus non finibus. Nulla ultricies est nulla, eget efficitur nibh viverra non. Sed sed est viverra nunc malesuada venenatis vitae at tellus. Suspendisse potenti. Morbi non blandit elit, sit amet consectetur mi.)</p>
<p>[subheading]Acme Website[/subheading]<br />
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent ac blandit risus. Mauris tempor a lacus a placerat. Vivamus viverra dapibus metus non finibus. Nulla ultricies est nulla, eget efficitur nibh viverra non. Sed sed est viverra nunc malesuada venenatis vitae at tellus. Suspendisse potenti. Morbi non blandit elit, sit amet consectetur mi., please contact us: [site_email]</p>
",
      [
        'heading' => function ($children) {
          return '<h2>' . $children . '</h2>';
        },
      ],
    );

    return $tests;
  }

  /**
   * @dataProvider dataForTestReplaceTagsWorksProvider
   */
  public function testReplaceTagsWorks($subject, $control, $map) {
    $this->assertSame($control, ShortCodes::inflate(
      $subject,
      $map
    ));
  }

  public function testReplaceTagsWorksWithNonSelfClosingElement() {
    $this->assertSame('<h2>Lorem</h2>', ShortCodes::inflate(
      '[header]Lorem[/header]',
      [
        'header' => function ($children) {
          return '<h2>' . $children . '</h2>';
        },
      ]
    ));
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testReplaceTagsThrowsWhenComponentIsNotUpperCamel() {
    ShortCodes::inflate('Here is [apple] to eat.', [
      'apple' => (object) ['#markup' => 'food'],
    ]);
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testReplaceTagsThrowsWhenComponentIsObjectAndCannotBeCastToString() {
    ShortCodes::inflate('Here is [apple] to eat.', [
      'apple' => (object) ['#markup' => 'food'],
    ]);
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testReplaceTagsThrowsWhenComponentIsArray() {
    ShortCodes::inflate('Here is [apple] to eat.', [
      'apple' => ['#markup' => 'food'],
    ]);
  }

  public function testReplaceTagsWithComponentNotAppearingInStringWorks() {
    $this->assertSame('This is totally left field.', ShortCodes::inflate(
      'This is totally left field.',
      [
        'apple' => 'an apple',
      ]
    ));
  }

  public function testReplaceTagsWithComponentStringWorks() {
    $this->assertSame('Here is an apple to eat.', ShortCodes::inflate(
      'Here is [apple] to eat.',
      [
        'apple' => 'an apple',
      ]
    ));
  }

  public function testReplaceTagsSelfClosingNoAttributesWorks() {
    $this->assertSame('Here is dinner to eat.', ShortCodes::inflate(
      'Here is [food] to eat.',
      [
        'food' => function () {
          return 'dinner';
        },
      ]));
  }

  public function testReplaceTagsSelfClosingWithAttributesWorks() {
    $this->assertSame('Here is an apple to eat.', ShortCodes::inflate(
      'Here is [food name="an apple" ] to eat.',
      [
        'food' => function ($inner, $attributes) {
          return $attributes['name'];
        },
      ]));
  }

}

/**
 * Used to test if an object is cast to string.
 */
class ShortcodesCastableString {

  public function __toString() {
    return 'a nice juicy pie';
  }

}
