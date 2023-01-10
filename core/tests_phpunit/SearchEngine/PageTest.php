<?php

namespace AKlump\LoftDocs\Tests\SearchEngine;

use AKlump\LoftDocs\SearchEngine\Page;
use AKlump\LoftDocs\SearchEngine\PageInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AKlump\LoftDocs\SearchEngine\Page
 */
class PageTest extends TestCase {

  public function testConstants() {
    $this->assertNotEmpty(PageInterface::DELIMITER_BEGIN);
    $this->assertNotEmpty(PageInterface::DELIMITER_END);
  }

  public function testUrlGetsNormalized() {
    $page = new Page('/some/path/');
    $this->assertSame('some/path', $page->getUrl());
  }

  public function testConstructor() {
    $page = new Page('', 'Lorem', 'alpha', ['do', 're']);
    $this->assertSame('Lorem', $page->getTitle());
    $this->assertSame('alpha', $page->getBody());
    $this->assertSame(['do', 're'], $page->getTags());
  }

  public function testAddTagsMergesCorrectlyWithConstructorTags() {
    $page = new Page('', '', '', ['foo']);
    $page->addTags(['bar', 'baz']);
    $page->addTags(['foo']);
    $this->assertCount(3, $page->getTags());
    $this->assertContains('foo', $page->getTags());
    $this->assertContains('bar', $page->getTags());
    $this->assertContains('baz', $page->getTags());
  }

  public function testAddTagsDoesNotDuplicateTags() {
    $page = new Page();
    $page->addTags(['foo']);
    $page->addTags(['foo']);
    $this->assertCount(1, $page->getTags());
  }

  public function testAddTagsMergesPrevious() {
    $page = new Page();
    $page->addTags(['foo']);
    $page->addTags(['bar']);
    $this->assertContains('foo', $page->getTags());
    $this->assertContains('bar', $page->getTags());
  }

  public function testAddTagsThrowsWithSpace() {
    $this->expectException(\InvalidArgumentException::class);
    $page = new Page();
    $page->addTags(['foo bar']);
  }

  public function testTagsWithSpacesThrows() {
    $this->expectException(\InvalidArgumentException::class);
    new Page('', '', '', array('my tag', 'yours'));
  }

}
