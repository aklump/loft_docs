<?php

namespace AKlump\LoftDocs\Tests\SearchEngine;

use AKlump\LoftDocs\SearchEngine\FromHtmlFactory;
use AKlump\LoftDocs\SearchEngine\Node;
use AKlump\LoftDocs\SearchEngine\PageFromHtmlFactory;
use AKlump\LoftDocs\SearchEngine\PageInterface;
use AKlump\LoftDocs\SearchEngine\SearchNodeInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AKlump\LoftDocs\SearchEngine\Node
 */
class PageFromHtmlFactoryTest extends TestCase {

  public function testTagsAsMetaKeywords() {
    $subject = <<<EOD
    <!DOCTYPE html>

    <html>
    <head>
      <title>Hooks</title>
      <link href="search/tipuesearch.css" rel="stylesheet">
      <link href="style.css" rel="stylesheet">
      <meta name="keywords" content="extending,pluggable" />
    </head>

    <body class="page--hooks">
    <header>
      <div class="pager"><a href="qs--partials.html" class="prev qs--partials">&laquo;Partials (Include Files)</a><a href="index.html" class="index pager__book-title">Loft Docs</a><a href="qs--version.html" class="next qs--version">Documentation Version&raquo;</a></div><    
    EOD;
    $factory = new PageFromHtmlFactory();
    $page = $factory($subject);
    $this->assertSame($page->getTags(), array('extending', 'pluggable'));
  }

  public function testDelimitersWorkToPullContent() {
    $subject = <<<EOD
    <!DOCTYPE html>\n\n<html>\n<head>\n  <title><\/title>\n  <link href="search\/tipuesearch.css" rel="stylesheet">\n  <link href="style.css" rel="stylesheet">\n<\/head>\n\n<body class="">\n<header>\n  <div class="pager"><a href="javascript:void(0)" class="prev ">&laquo;<\/a><a href="index.html" class="index pager__book-title"><\/a><a href="javascript:void(0)" class="next ">&raquo;<\/a><\/div><\/header>\n<section class="sub-header">\n  <div class="breadcrumbs"><a href="index.html">Index<\/a>\n         &raquo\n      <\/div>      <div class="search__wrapper">\n  <form action="search--results.html">\n    <input type="text" class="search-input" name="q" id="tipue_search_input" autocomplete="off" required>\n  <\/form>\n<\/div>\n  <\/section>\n\n<section>\n  <h1><\/h1>\n  <!--searchable_content-->We never went to the moon.\n<!--end_searchable_content-->\n<\/section>\n\n<div class="search__results">\n  <div id="tipue_search_content"><\/div>\n<\/div>\n\n<footer>\n  <div class="pager"><a href="javascript:void(0)" class="prev ">&laquo;<\/a><a href="index.html" class="index pager__book-title"><\/a><a href="javascript:void(0)" class="next ">&raquo;<\/a><\/div>  \n  <div id="footer-legaleeze">\n    Version: 1.0 &bull; Last Updated: Tue, 10 Jan 2023 17:33:02 -0800\n\n  <\/div>\n<\/footer>\n\n<script src="https:\/\/ajax.googleapis.com\/ajax\/libs\/jquery\/2.1.4\/jquery.min.js"><\/script>\n  <script src="search\/tipuesearch_content.js"><\/script>\n  <script src="search\/tipuesearch_set.js"><\/script>\n  <script src="search\/tipuesearch.min.js"><\/script>\n  <script>\n    $(document).ready(function() {\n      $(\'#tipue_search_input\').tipuesearch()\n    })\n  <\/script>\n<script src="js\/core.js"><\/script>\n<\/body>\n<\/html>    
    EOD;
    $factory = new PageFromHtmlFactory();
    $page = $factory($subject);
    $this->assertSame('We never went to the moon.', $page->getBody());
  }

  public function testBasicHtml() {
    $page_factory = new PageFromHtmlFactory();
    $html = '<h1>The Title</h1><p>Lorem ipsum dolar</p>';
    $page = $page_factory($html);
    $this->assertSame('The Title', $page->getTitle());
    $this->assertSame('Lorem ipsum dolar', $page->getBody());
    $this->assertEmpty($page->getTags());
  }

  public function testCorrectInstanceClassIsReturned() {
    $factory = new PageFromHtmlFactory();
    $node = $factory('foo');
    $this->assertInstanceOf(PageInterface::class, $node);
  }

}
