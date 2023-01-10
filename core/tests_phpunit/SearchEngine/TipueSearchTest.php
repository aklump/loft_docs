<?php

namespace AKlump\LoftDocs\Tests\SearchEngine;

use AKlump\LoftDocs\SearchEngine\Page;
use AKlump\LoftDocs\SearchEngine\SearchEngineFile;
use PHPUnit\Framework\TestCase;
use AKlump\LoftDocs\SearchEngine\TipueSearch;

/**
 * @covers \TipueSearch
 */
class TipueSearchTest extends TestCase {

  public function testAddTwoSortsCorrectly() {
    $obj = new TipueSearch();
    $obj
      ->addPage(new Page('/bravo.html/', 'bravo', 'contents', array(
        'fa',
        'so',
        'la',
      )))
      ->addPage(new Page('alpha.html', 'alpha', 'contents', array(
        'do',
        're',
        'mi',
      )));
    $file = self::getContentFile($obj);
    $control = <<<EOD
var tipuesearch = {"pages":[{"title":"alpha","text":"contents","tags":"do re mi","url":"alpha.html"},{"title":"bravo","text":"contents","tags":"fa so la","url":"bravo.html"}]};

EOD;
    $this->assertSame($control, $file->getContents());
  }

  public function testAddDuplicatesFilters() {
    $obj = new TipueSearch();
    $obj->addPage(new Page('/page.html', 'title', 'contents', array(
      'do',
      're',
      'mi',
    )));
    $obj->addPage(new Page('/page.html', 'title', 'contents', array(
      'do',
      're',
      'mi',
    )));
    $file = self::getContentFile($obj);
    $control = <<<EOD
var tipuesearch = {"pages":[{"title":"title","text":"contents","tags":"do re mi","url":"page.html"}]};

EOD;
    $this->assertSame($control, $file->getContents());
  }

  public function testAddOneGet() {
    $obj = new TipueSearch();
    $obj->addPage(new Page('/page.html', 'title', 'contents', array(
      'do',
      're',
      'mi',
    )));
    $file = self::getContentFile($obj);

    $control = <<<EOD
var tipuesearch = {"pages":[{"title":"title","text":"contents","tags":"do re mi","url":"page.html"}]};

EOD;
    $this->assertSame($control, $file->getContents());
  }

  public static function getContentFile(TipueSearch $obj) {
    $files = array_filter($obj->getFiles(), function (SearchEngineFile $file) {
      return 'tipuesearch_content.js' === $file->getFilename();
    });

    return array_values($files)[0];
  }

}
