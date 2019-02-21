<?php
/**
 * @file
 * PHPUnit tests for the ConfigBash class
 */
namespace AKlump\LoftLib\Config;

require_once dirname(__FILE__) . '/../../../../../vendor/autoload.php';

class ConfigBashTest extends FileBasedConfigTestBase
{
    public function testSetNumericArrayPlusFormat()
    {
        $obj = new ConfigBash($this->dir, null, array('install' => true));
        $obj->write('a', array('p', 'b'));
        $this->assertSame(array('p', 'b'), $obj->read('a'));

        // Check the file contents format.
        $contents = file_get_contents($obj->getStorage()->value);
        $control = '#!/bin/bash' . PHP_EOL . 'a=("p" "b")' . PHP_EOL;
        $this->assertSame($control, $contents);
    }

    public function testGetSet()
    {
        $obj = new ConfigBash($this->dir, null, array('install' => true));
        $obj->write('a', 'aaron');
        $obj->write('b', 'brian');
        $this->assertSame('aaron', $obj->read('a'));
        $this->assertSame('brian', $obj->read('b'));
    }

    /**
     * Provides data for testNonScalars.
     */
    public function DataForTestNonScalarsProvider()
    {
        $tests = array();
        $tests[] = array(array('do' => 're'));
        $tests[] = array((object) array('do' => 're'));

        return $tests;
    }

    /**
     * @dataProvider DataForTestNonScalarsProvider
     * @expectedException InvalidArgumentException
     */
    public function testNonScalars($value)
    {
        $obj = new ConfigBash($this->dir, null, array('install' => true));
        $obj->write('a', $value);
    }

    public function testFileFormatIsCorrect()
    {
        $obj = new ConfigBash($this->dir, null, array('install' => true));
        $obj->write('a', 'alpha');
        $obj->write('b', 'bravo charlie');
        $contents = file_get_contents($obj->getStorage()->value);
        $control = "#!/bin/bash\na=\"alpha\"\nb=\"bravo charlie\"\n";
        $this->assertSame($control, $contents);
        $obj->destroy();
    }
}
