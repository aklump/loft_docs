<?php

namespace AKlump\LoftDocs\Tests\SearchEngine;

use AKlump\LoftDocs\SearchEngine\Page;
use AKlump\LoftDocs\SearchEngine\PageInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AKlump\LoftDocs\SearchEngine\Page
 */
class PageInterfaceTest extends TestCase {

  public function testConstants() {
    $this->assertNotEmpty(PageInterface::DELIMITER_BEGIN);
    $this->assertNotEmpty(PageInterface::DELIMITER_END);
  }

}
