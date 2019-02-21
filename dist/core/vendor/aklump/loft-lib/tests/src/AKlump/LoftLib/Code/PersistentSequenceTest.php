<?php

namespace AKlump\LoftLib\Code;


use AKlump\LoftLib\Testing\PhpUnitTestCase;

class PersistentSequenceTest extends PhpUnitTestCase {

    protected $obj;

    public function testNext()
    {
        $session = [];
        list($path, $dataset) = $this->objArgs;

        $result = PersistentSequence::next($path, $dataset, $session);
        $this->assertSame('do', $result);

        $result = PersistentSequence::next($path, $dataset, $session);
        $this->assertSame('re', $result);

        $result = PersistentSequence::next($path, $dataset, $session);
        $this->assertSame('mi', $result);
    }

    public function testGetNextWorksAsExpected()
    {
        $this->assertSame('do', $this->obj->getNext());
        $this->assertSame('re', $this->obj->getNext());
        $this->assertSame('mi', $this->obj->getNext());
        $this->assertSame('do', $this->obj->getNext());
        $this->assertSame('re', $this->obj->getNext());
        $this->assertSame('mi', $this->obj->getNext());
        $this->assertSame('do', $this->obj->getNext());
        $this->assertSame('re', $this->obj->getNext());
        $this->assertSame('mi', $this->obj->getNext());
    }

    public function setUp()
    {
        $this->objArgs = [
            'list',
            [
                // Do not change this area or it will break tests.
                'do',
                're',
                'mi',
            ],
            [],
        ];
        list($path, $dataset) = $this->objArgs;
        $this->obj = new PersistentSequence($path, $dataset, $this->objArgs[2]);
    }
}
