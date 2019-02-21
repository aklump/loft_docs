<?php

namespace AKlump\LoftLib\Bash;

class ColorTest extends \PHPUnit_Framework_TestCase {

  public function testStart() {
    $output = Color::start('white on blue');
    $this->assertSame("\e[1;37m\e[44m", $output);
  }

  public function testStop() {
    $this->assertSame("\e[0m", Color::stop());
  }

  public function testWrap() {
    $output = Color::wrap('blue', 'lorem ipsum');
    $this->assertSame("\e[0;34mlorem ipsum\e[0m", $output);
    $output = Color::wrap('blue', 'lorem ipsum', 1);
    $this->assertSame("\e[1;34mlorem ipsum\e[0m", $output);
    $output = Color::wrap('white on blue', 'lorem ipsum');
    $this->assertSame("\e[1;37m\e[44mlorem ipsum\e[0m", $output);
  }

}
