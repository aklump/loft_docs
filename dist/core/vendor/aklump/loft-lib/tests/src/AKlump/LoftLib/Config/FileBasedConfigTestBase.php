<?php
/**
 *
 */

namespace AKlump\LoftLib\Config;


class FileBasedConfigTestBase extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->dir = realpath(dirname(__FILE__) . '/../../../../sandbox');
        $this->assertTrue(is_dir($this->dir));

        $this->classname = preg_replace('/Test$/', '', get_class($this));
    }

    public function tearDown()
    {
        $obj = new $this->classname($this->dir);
        $obj->destroy();
    }
}
