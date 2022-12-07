<?php

namespace AKlump\LoftDocs\Tests;

use PHPUnit\Framework\TestCase;
use AKlump\LoftDocs\SearchPageData;

/**
 * @covers \AKlump\LoftDocs\SearchPageData
 */
class SearchPageDataTest extends TestCase {

  public function testTagsWithSpacesThrows() {
    $this->expectException(\Exception::class);
    new SearchPageData('', '', '', array('my tag', 'yours'));
  }

}
