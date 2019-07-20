<?php

namespace AKlump\LoftLib\Code;

use PHPUnit\Framework\TestCase;

class ObjectCacheTraitTest extends TestCase {

  public function testDefaultValueIsReturned() {
    $this->assertSame('lie', $this->obj->setValue('false', 'news')
      ->clearByKey('false')
      ->getValue('false', 'lie'));
  }

  public function testAbleToClearCacheWithoutKey() {
    $this->assertEmpty($this->obj->setValue('false', 'news')
      ->setValue('exaggerated', 'stories')
      ->clear()
      ->getValue('false', NULL));
    $this->assertEmpty($this->obj->getValue('exaggerated', NULL));
  }

  public function testAbleToClearCacheByKey() {
    $this->assertEmpty($this->obj->setValue('false', 'news')
      ->setValue('exaggerated', 'stories')
      ->clearByKey('false')
      ->getValue('false', NULL));
    $this->assertSame('stories', $this->obj->getValue('exaggerated', NULL));
  }

  public function testAbleToSetAndGetUsingCacheMethods() {
    $this->assertSame('news', $this->obj->setValue('false', 'news')
      ->getValue('false', 'lie'));
  }

  public function setUp() {
    $this->obj = new ObjectCacheTraitTestSubject();
  }
}

class ObjectCacheTraitTestSubject {

  use ObjectCacheTrait;

  public function getValue($key, $default) {
    return $this->getCached($key, $default);
  }

  public function setValue($key, $value) {
    return $this->setCached($key, $value);
  }

  public function clear() {
    return $this->clearCached();
  }

  public function clearByKey($key) {
    return $this->clearCached($key);
  }

}
