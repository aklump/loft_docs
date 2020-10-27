<?php

namespace AKlump\LoftLib\Code;

use AKlump\LoftLib\Testing\PhpUnitTestCase;

class CustomTagsTest extends PhpUnitTestCase {

  public function testReplaceTagsWorksWhenComponentIsObjectAndCanBeCastToString() {
    $this->assertSame('Here is a nice juicy pie to eat.', CustomTags::replaceTags('Here is <Apple/> to eat.', [
      'Apple' => new CastableString(),
    ]));
  }

  public function testGetElementsOnEmptyStringReturnsEmptyArray() {
    $this->assertSame([], CustomTags::getElements(''));
  }

  public function testGetElementsWorksWithoutAttributes() {
    $control = [
      0 => [
        'name' => 'Heading',
        'inner_html' => 'Project Summary',
        'attributes' => [],
      ],
    ];
    $subject = '<p><Heading>Project Summary</Heading></p>';

    $this->assertSame($control, CustomTags::getElements($subject));
  }

  public function testGetElementsWorksAsExpected() {
    $control = [
      0 => [
        'name' => 'Anchor',
        'inner_html' => '',
        'attributes' => [
          'layout' => 'right',
          'color' => 'red',
        ],
      ],
      1 => [
        'name' => 'Person',
        'inner_html' => 'Aaron',
        'attributes' => [
          'type' => 'leader',
          'age' => 44,
        ],
      ],
    ];
    $subject = 'Fusce vel sapien quis orci feugiat accumsan vel sit amet massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nunc varius turpis vel placerat eleifend. Vivamus tempus enim quam, sit amet porta libero efficitur ac. Maecenas ultricies, felis id vulputate consectetur, <Anchor layout="right" color="red"/>ligula ligula tempor augue, et feugiat sapien ante sit amet dui. Morbi ullamcorper justo nec purus cursus ullamcorper. Sed semper dictum tellus, vel varius metus pellentesque eu. Ut interdum tristique finibus. In pharetra nibh a malesuada dignissim. Etiam a interdum orci. Maecenas ultricies porttitor <Person type="leader" age="44">Aaron</Person>neque. Quisque sit amet tincidunt nulla, ut aliquam mauris. Pellentesque ut efficitur eros. Aenean vestibulum aliquet odio, quis pellentesque mauris congue id. Aenean vitae turpis id sapien sollicitudin blandit.';

    $this->assertSame($control, CustomTags::getElements($subject));
  }

  /**
   * Provides data for testReplaceTagsWorks.
   */
  public function dataForTestReplaceTagsWorksProvider() {
    $tests = array();

    // Self closing inside of of nested HTML.
    $tests[] = array(
      '<div><span class="bold">Here is <Food/> to eat.</span></div>',
      '<div><span class="bold">Here is dinner to eat.</span></div>',
      [
        'Food' => function () {
          return 'dinner';
        },
      ],
    );

    // Non self-closing inside of nexted HTML.
    $tests[] = array(
      "<p><Heading>Showcasing Student Work</Heading><br />
Showcasing student work can be an opportunity to promote thoughtful dialogue to encourage students to voice their own power of perspective. Photographs can be shared with students' classmates, family members, and communities in a variety of ways.</p>
<p><Subheading>Gallery Walk</Subheading><br />
Students can print their photographs and display them around the classroom or in a public area. Photographs can also be displayed in a slideshow at an event.</p>
<p><Subheading>Social Media</Subheading><br />
Students can post their photographs and captions on <a href=\"http://develop.globalonenessproject.loft/\">Twitter</a> or <a href=\"http://develop.globalonenessproject.loft/\">Instagram</a> with the <strong>#RememberEarth</strong>. Photographs might be selected and reposted on the Global Oneness Project's social media platforms. (Note: This activity is for students 13 years of age and older due to social media restrictions.)</p>
<p><Subheading>Global Oneness Project Website</Subheading><br />
We are collecting student photographs to be considered for publication on our website, please contact us: <SiteEmail/></p>
",
      "<p><h2>Showcasing Student Work</h2><br />
Showcasing student work can be an opportunity to promote thoughtful dialogue to encourage students to voice their own power of perspective. Photographs can be shared with students' classmates, family members, and communities in a variety of ways.</p>
<p><Subheading>Gallery Walk</Subheading><br />
Students can print their photographs and display them around the classroom or in a public area. Photographs can also be displayed in a slideshow at an event.</p>
<p><Subheading>Social Media</Subheading><br />
Students can post their photographs and captions on <a href=\"http://develop.globalonenessproject.loft/\">Twitter</a> or <a href=\"http://develop.globalonenessproject.loft/\">Instagram</a> with the <strong>#RememberEarth</strong>. Photographs might be selected and reposted on the Global Oneness Project's social media platforms. (Note: This activity is for students 13 years of age and older due to social media restrictions.)</p>
<p><Subheading>Global Oneness Project Website</Subheading><br />
We are collecting student photographs to be considered for publication on our website, please contact us: <SiteEmail/></p>
",
      [
        'Heading' => function ($children) {
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
    $this->assertSame($control, CustomTags::replaceTags(
      $subject,
      $map
    ));
  }

  public function testReplaceTagsWorksWithNonSelfClosingElement() {
    $this->assertSame('<h2>Lorem</h2>', CustomTags::replaceTags(
      '<Header>Lorem</Header>',
      [
        'Header' => function ($children) {
          return '<h2>' . $children . '</h2>';
        },
      ]
    ));
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testReplaceTagsThrowsWhenComponentIsNotUpperCamel() {
    CustomTags::replaceTags('Here is <apple/> to eat.', [
      'apple' => (object) ['#markup' => 'food'],
    ]);
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testReplaceTagsThrowsWhenComponentIsObjectAndCannotBeCastToString() {
    CustomTags::replaceTags('Here is <Apple/> to eat.', [
      'Apple' => (object) ['#markup' => 'food'],
    ]);
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testReplaceTagsThrowsWhenComponentIsArray() {
    CustomTags::replaceTags('Here is <Apple/> to eat.', [
      'Apple' => ['#markup' => 'food'],
    ]);
  }

  public function testReplaceTagsWithComponentNotAppearingInStringWorks() {
    $this->assertSame('This is totally left field.', CustomTags::replaceTags(
      'This is totally left field.',
      [
        'Apple' => 'an apple',
      ]
    ));
  }

  public function testReplaceTagsWithComponentStringWorks() {
    $this->assertSame('Here is an apple to eat.', CustomTags::replaceTags(
      'Here is <Apple/> to eat.',
      [
        'Apple' => 'an apple',
      ]
    ));
  }

  public function testReplaceTagsSelfClosingNoAttributesWorks() {
    $this->assertSame('Here is dinner to eat.', CustomTags::replaceTags(
      'Here is <Food/> to eat.',
      [
        'Food' => function () {
          return 'dinner';
        },
      ]));
  }

  public function testReplaceTagsSelfClosingWithAttributesWorks() {
    $this->assertSame('Here is an apple to eat.', CustomTags::replaceTags(
      'Here is <Food name="an apple" /> to eat.',
      [
        'Food' => function ($inner, $attributes) {
          return $attributes['name'];
        },
      ]));
  }

}

/**
 * Used to test if an object is cast to string.
 */
class CastableString {

  public function __toString() {
    return 'a nice juicy pie';
  }

}
