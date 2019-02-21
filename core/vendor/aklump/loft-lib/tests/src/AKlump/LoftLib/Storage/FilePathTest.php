<?php

/**
 * @file
 * PHPUnit tests for the FilePath class
 */

namespace AKlump\LoftLib\Storage;

use AKlump\LoftLib\Code\LoftXmlElement;
use AKlump\LoftLib\Testing\PhpUnitTestCase;

class FilePathTest extends PhpUnitTestCase {

  public function testCopyFromWithDifferentBasenames() {
    $source = new FilePath($this->sb . '/alpha/index.html');
    $source->put('do')->save();

    $destination = new FilePath($this->sb . '/bravo/index_bu.html');
    $destination->copyFrom($source);

    $this->assertFileExists($this->sb . '/alpha/index.html');
    $this->assertFileNotExists($this->sb . '/bravo/index.html');
    $this->assertFileExists($this->sb . '/bravo/index_bu.html');
    $this->assertSame('do', file_get_contents($this->sb . '/bravo/index_bu.html'));
  }

  public function testGetHash() {
    $hash = FilePath::create($this->sb . '/hash/demo.json')
      ->putJson([
        'weather' => 'cloudy',
        'temperature' => 'mild',
      ])
      ->save()
      ->getHash();
    $this->assertSame('04f11f47db99ac6707931d78d70b418c', $hash);
  }

  public function testConstructWithExtensionGeneratesTempNamePath() {
    $obj = new FilePath($this->sb, ['extension' => 'pdf']);
    $path = $obj->getPath();
    $info = pathinfo($path);
    $this->assertSame('pdf', $info['extension']);
    $this->assertSame($this->sb, $info['dirname'] . '/');
    $this->assertNotEmpty($info['filename']);

    $obj = new FilePath($this->sb, ['extension' => '.pdf']);
    $path = $obj->getPath();
    $info = pathinfo($path);
    $this->assertSame('pdf', $info['extension']);
    $this->assertSame($this->sb, $info['dirname'] . '/');
    $this->assertNotEmpty($info['filename']);
  }

  public function testMoveFrom() {
    $control = 'do.re.mi';
    $source = new FilePath($this->sb . '/alpha/index.html');
    $source->put($control)->save();

    $destination = new FilePath($this->sb . '/bravo');
    $destination->moveFrom($source);

    $this->assertFileNotExists($this->sb . '/alpha/index.html');
    $this->assertFileExists($this->sb . '/bravo/index.html');
    $this->assertSame($control, file_get_contents($this->sb . '/bravo/index.html'));
  }

  /**
   * @dataProvider DataForTestChildrenAndDescendentsReturnCorrectLevelsProvider
   */
  public function testChildrenAndDescendentsReturnCorrectLevels($method, $args, $control) {
    // Setup a testing file structure.
    $dir = new FilePath($this->sb);
    $dir->put('tbd');
    $dir->to('do.json')->save();
    $dir->to('re.pdf')->save();

    $grandChild = new FilePath($this->sb . 'alpha/mi.txt');
    $grandChild->put('tbd')->save();

    $greatGrandChild = new FilePath($this->sb . 'alpha/bravo/fa.txt');
    $greatGrandChild->put('tbd')->save();

    // Now run the test based on the data provided.
    $sb = new FilePath($this->sb);
    $list = call_user_func_array([$sb, $method], $args)->paths($this->sb);
    foreach ($control as $key => $item) {
      $this->assertSame($item, $list[$key]);
    }
    $this->assertCount(count($control), $list);
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testConstructWithExtensionAndPathToFileThrows() {
    new FilePath($this->sb . '/temp.pdf', ['extension' => 'pdf']);
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testConstructWithFilenameAsExtensionThrows() {
    new FilePath($this->sb, ['extension' => 'temp.pdf']);
  }

  public function testFileThatHasNoExtensionAndDoesntExistWorksOkay() {
    $cli = new FilePath($this->sb . '/controller', [
      'is_dir' => FALSE,
    ]);
    $this->assertSame(FilePath::TYPE_FILE, $cli->getType());

    $cli = new FilePath($this->sb . '/controller', [
      'type' => FilePath::TYPE_FILE,
    ]);
    $this->assertSame(FilePath::TYPE_FILE, $cli->getType());
  }

  /**
   * @expectedException \Exception
   */
  public function testEnsureDirCantWriteThrows() {
    $path = FilePath::create($this->sb . 'do')->parents();
    chmod($path->getPath(), '0000');
    FilePath::ensureDir($this->sb . 'do/re');
  }

  public function testLoadOnFileAllowsMultipleTosToDifferntFilesWithSameContent() {
    $path = $this->sb . 'do/re/mi';
    FilePath::ensureDir($path);

    $file_path = "$path/read.txt";
    file_put_contents($file_path, 'stamp');

    $file = new FilePath($file_path);
    $file->load();

    $this->assertSame('stamp', file_get_contents($file->to('fish.txt')
      ->save()
      ->getPath()));
    $this->assertSame('stamp', file_get_contents($file->to('goat.txt')
      ->save()
      ->getPath()));
    $this->assertSame('stamp', file_get_contents($file->to('eagle.txt')
      ->save()
      ->getPath()));
  }

  public function testPutBaseWithoutHeaderInDataAddsHeaderAndInsuresEndOfFileNewline() {
    $control = $subject = [
      '#!/usr/bin/env bash',
      'echo "hello world"',
      '',
    ];
    array_shift($subject);
    array_pop($subject);
    $obj = new FilePath($this->sb, ['extension' => 'sh']);
    $obj->putBash($subject)->save();

    $contents = file_get_contents($obj->getPath());
    $this->assertSame(implode(PHP_EOL, $control), $contents);
  }

  /**
   * @expectedException \RuntimeException
   */
  public function testFromOnFileThrows() {
    $file = new FilePath($this->sb, ['extension' => 'json']);
    $file->from('bravo.json');
  }

  public function testPutBaseWithHeaderInDataDoesntDoubleHeader() {
    $data = [
      '#!/usr/bin/env bash',
      'echo "hello world"',
      '',
    ];
    $obj = new FilePath($this->sb, ['extension' => 'sh']);
    $obj->putBash($data)->save();

    $contents = file_get_contents($obj->getPath());
    $this->assertSame(implode(PHP_EOL, $data), $contents);
  }

  public function testDestroyFilepath() {
    $obj = new FilePath($this->sb, ['extension' => 'pdf']);
    $this->assertSame(FilePath::TYPE_FILE, $obj->getType());
    $path = $obj->put('do')->save()->getPath();
    $this->assertFileExists($path);
    $obj->destroy();
    $this->assertFileNotExists($path);
  }

  public function testChildrenFindsDirsInDirWhenNoFilesInDir() {
    $sb = new FilePath($this->sb);
    $dir = $sb->getPath() . '/' . __FUNCTION__;

    // Create a folder with just folders.
    mkdir($dir);
    mkdir($dir . '/alpha');
    mkdir($dir . '/bravo');
    mkdir($dir . '/charlie');

    $obj = new FilePath($dir);
    $list = $obj->children();
    $this->assertCount(3, $list);

    rmdir($dir . '/alpha');
    rmdir($dir . '/bravo');
    rmdir($dir . '/charlie');
    rmdir($dir);
  }

  public function testEnsureDirCreatesDirectionFromDotPath() {
    FilePath::ensureDir($this->sb . '/.taskcamp', NULL, TRUE);
    $this->assertTrue(is_dir($this->sb . '/.taskcamp'));
  }

  public function testUseIsDirOptionCreatesDirectionFromDotPath() {
    $sb = FilePath::create($this->sb . '/.taskcamp', ['is_dir' => TRUE])
      ->parents();
    $this->assertTrue(is_dir($sb->getPath()));
  }

  public function testInstallReturnsThis() {
    $sb = new FilePath($this->sb, ['install' => FALSE]);
    $this->assertSame($sb, $sb->parents());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testChildrenPassNullArgumentThrows() {
    $sb = new FilePath($this->sb);
    $sb->children(NULL);
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testChildrenPassObjectArgumentThrows() {
    $sb = new FilePath($this->sb);
    $sb->children(new \stdClass());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testChildrenPassArrayArgumentThrows() {
    $sb = new FilePath($this->sb);
    $sb->children([]);
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testChildrenPassFloatArgumentThrows() {
    $sb = new FilePath($this->sb);
    $sb->children(1.45);
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testChildrenPassBooleanArgumentThrows() {
    $sb = new FilePath($this->sb);
    $sb->children(FALSE);
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testDescendentsPassNullArgumentThrows() {
    $sb = new FilePath($this->sb);
    $sb->descendents(NULL);
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testDescendentsPassObjectArgumentThrows() {
    $sb = new FilePath($this->sb);
    $sb->descendents(new \stdClass());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testDescendentsPassArrayArgumentThrows() {
    $sb = new FilePath($this->sb);
    $sb->descendents([]);
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testDescendentsPassFloatArgumentThrows() {
    $sb = new FilePath($this->sb);
    $sb->descendents(1.45);
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testDescendentsPassBooleanArgumentThrows() {
    $sb = new FilePath($this->sb);
    $sb->descendents(FALSE);
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testEnsureDirectoryWithEmptyPathThrows() {
    FilePath::ensureDir('');
  }

  public function testPathsOnCollectionWithLTrimArgumentRemovesLeadingStringFromValues() {
    $list = new FilePathCollection([
      new FilePath($this->sb . 'do'),
      new FilePath($this->sb . 're'),
      new FilePath($this->sb . 're/mi.txt'),
      new FilePath($this->sb . 'fa.json'),
    ]);
    $this->assertSame([
      'do',
      're',
      're/mi.txt',
      'fa.json',
    ], $list->paths($this->sb));
  }

  public function testJustDirsAndJustPathsReturnAsExpected() {
    $list = new FilePathCollection([
      new FilePath($this->sb . 'do'),
      new FilePath($this->sb . 're'),
      new FilePath($this->sb . 're/mi.txt'),
      new FilePath($this->sb . 'fa.json'),
    ]);

    $dirs = $list->justDirs()->all();
    $files = $list->justFiles()->all();

    $this->assertSame($this->sb . 'do', $dirs[0]->getPath());
    $this->assertSame($this->sb . 're', $dirs[1]->getPath());

    $this->assertSame($this->sb . 're/mi.txt', $files[0]->getPath());
    $this->assertSame($this->sb . 'fa.json', $files[1]->getPath());
  }

  /**
   * Provides data for testChildrenAndDescendentsReturnCorrectLevels.
   */
  public function DataForTestChildrenAndDescendentsReturnCorrectLevelsProvider() {
    $tests = array();
    $tests[] = [
      'descendents',
      [2],
      ['alpha', 'alpha/bravo', 'alpha/mi.txt', 'do.json', 're.pdf'],
    ];
    // Exclusive by regex with 0 as the level limit.
    $tests[] = [
      'descendents',
      [0, '/.*/', ''],
      [
        'alpha',
        'alpha/bravo',
        'alpha/bravo/fa.txt',
        'alpha/mi.txt',
        'do.json',
        're.pdf',
      ],
    ];

    // Exclusive by regex with 0 as the level limit.
    $tests[] = [
      'descendents',
      ['', '/\.pdf$/', 0],
      [
        'alpha',
        'alpha/bravo',
        'alpha/bravo/fa.txt',
        'alpha/mi.txt',
        'do.json',
      ],
    ];

    // Inclusive and exclusive by regex with null as the level limit.
    $tests[] = [
      'descendents',
      ['/bravo/', 0, '/\.pdf$/'],
      [
        'alpha/bravo',
        'alpha/bravo/fa.txt',
      ],
    ];


    // Inclusive by regex with false as the level limit.
    $tests[] = ['descendents', ['/\.pdf$/'], ['re.pdf']];
    $tests[] = [
      'descendents',
      [3],
      [
        'alpha',
        'alpha/bravo',
        'alpha/bravo/fa.txt',
        'alpha/mi.txt',
        'do.json',
        're.pdf',
      ],
    ];
    $tests[] = [
      'descendents',
      ['', '', 0],
      [
        'alpha',
        'alpha/bravo',
        'alpha/bravo/fa.txt',
        'alpha/mi.txt',
        'do.json',
        're.pdf',
      ],
    ];

    $tests[] = ['descendents', [1], ['alpha', 'do.json', 're.pdf']];
    $tests[] = ['children', [], ['alpha', 'do.json', 're.pdf']];

    return $tests;
  }

  public function testPutOnDirAllowsMultipleTosToDifferntFilesWithSameContent() {
    $path = $this->sb . 'do/re/mi';
    $dir = new FilePath($path);

    $dir->put('stamp');

    $this->assertSame('stamp', file_get_contents($dir->to('fish.txt')
      ->save()
      ->getPath()));
    $this->assertSame('stamp', file_get_contents($dir->to('goat.txt')
      ->save()
      ->getPath()));
    $this->assertSame('stamp', file_get_contents($dir->to('eagle.txt')
      ->save()
      ->getPath()));
  }

  public function testUsingToOnDirDoesntAffectDirObjectAndDoesntCreateFile() {
    $path = $this->sb . 'do/re/mi';
    $dir = new FilePath($path);

    $file = $dir->put('bla')->to('fa.txt');
    $this->assertSame($path, $dir->getPath());

    $file_path = $path . '/fa.txt';
    $this->assertSame($file_path, $file->getPath());
    $this->assertFileNotExists($file_path);
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testOnFolderToWithEmptyArgThrows() {
    $file = new FilePath($this->sb);
    $file->to('');
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testOnFileToWithEmptyArgThrows() {
    $file = new FilePath($this->sb, ['extension' => 'json']);
    $file->to('');
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testSendingAPathToToThrows() {
    $dir = new FilePath($this->sb);
    $dir->put('<tree/>')->to('do/re/data.xml');
  }

  public function testToReturnsPathToANewFileObjectWhenFile() {
    $file = new FilePath($this->sb, ['extension' => 'json']);
    $renamed = $file->put('bla')->to('bravo.json');
    $this->assertNotSame($file, $renamed);
  }

  public function testToReturnsPathToANewFileObjectWhenDir() {
    $dir = new FilePath($this->sb . 'alpha');
    $file = $dir->put('bla')->to('bravo.json');
    $this->assertNotSame($dir, $file);
  }

  public function testFromReturnsNewFileObjectWhenDir() {
    $dir = new FilePath($this->sb . 'alpha');
    $file = $dir->from('bla.txt');
    $this->assertNotSame($dir, $file);
  }

  public function putBashDataWithAltHeaderWithAlternateHeaderArgumentDoesntDoubleAlternateHeader() {
    $control = $subject = [
      '#!/bin/cat',
      'Hello world!',
      '',
    ];
    $obj = new FilePath($this->sb, ['extension' => 'sh']);
    $obj->putBash($subject)->save();

    $contents = file_get_contents($obj->getPath());
    $this->assertSame(implode(PHP_EOL, $control), $contents);
  }

  public function putBashDataNoHeaderWithAlternateHeaderArgumentAppendsAlternateHeader() {
    $control = $subject = [
      '#!/bin/cat',
      'Hello world!',
      '',
    ];
    array_shift($subject);
    $obj = new FilePath($this->sb, ['extension' => 'sh']);
    $obj->putBash($subject)->save();

    $contents = file_get_contents($obj->getPath());
    $this->assertSame(implode(PHP_EOL, $control), $contents);
  }

  public function testGetJsonWithTrueArgumentReturnsArrayFalseReturnsObjectDefaultsToObject() {
    $data = ['do' => 're'];
    $path = $this->sb . 'data.json';
    file_put_contents($path, json_encode($data));
    $this->assertFileExists($path);
    $obj = new FilePath($path);
    $this->assertInternalType('array', $obj->load()->getJson(TRUE));
    $this->assertInternalType('object', $obj->load()->getJson(FALSE));
    $this->assertInternalType('object', $obj->load()->getJson());

  }

  public function testGetDownloadHeadersWithAlias() {
    $gif = new FilePath($this->sb . '/image.gif');
    $gif->put('GIF87')->save();
    $this->assertFalse($gif->hasAlias());
    $this->assertSame($gif, $gif->setAlias('my-cool-pix.gif'));
    $this->assertTrue($gif->hasAlias());
    $this->assertSame('my-cool-pix.gif', $gif->getAlias());
    $control = array(
      'Content-Type' => 'image/gif',
      'Content-Length' => 5,
      'Content-Disposition' => 'attachment; filename="my-cool-pix.gif"',
    );
    $this->assertSame($control, $gif->getDownloadHeaders());
  }

  /**
   * Provides data for testMimeTypes.
   */
  function DataForTestMimeTypesProvider() {
    $tests = array();
    $tests[] = array(
      'image.gif',
      'GIF87',
      'image/gif',
    );
    $tests[] = array(
      'words.txt',
      'do re mi',
      'text/plain',
    );
    $tests[] = array(
      'page.html',
      '<h1>title</h1>',
      'text/html',
    );
    $tests[] = array(
      'data.json',
      '{"do":"re"}',
      'application/json',
    );

    return $tests;
  }

  /**
   * @dataProvider DataForTestMimeTypesProvider
   */
  public function testMimeTypes($filename, $contents, $mime) {
    $obj = new FilePath($this->sb . '/' . $filename);
    $obj->put($contents)->save();
    $this->assertSame($mime, $obj->getMimeType());
    $obj->destroy();

  }

  public function testPathInfoForFile() {
    $obj = new FilePath($this->sb . '/demo.txt');
    $this->assertSame('demo.txt', $obj->getBasename());
    $this->assertSame('demo', $obj->getFilename());
    $this->assertSame('txt', $obj->getExtension());
    $this->assertSame($this->sb, $obj->getDirname() . '/');
  }

  public function testPathInfoForDirectory() {
    $obj = new FilePath($this->sb);
    $this->assertSame(NULL, $obj->getBasename());
    $this->assertSame(NULL, $obj->getFilename());
    $this->assertSame(NULL, $obj->getExtension());
    $this->assertSame($this->sb, $obj->getDirname() . '/');
  }

  public function testDateNameWithExtensionAndDateObject() {
    $subject = date_create('2017-03-16T11:38:00Z');
    $a = FilePath::dateName('xml', NULL, $subject);
    $this->assertSame('2017-03-16T11-38-00Z.xml', $a);
  }

  public function testDateName() {
    $a = FilePath::dateName();
    sleep(1);
    $b = FilePath::dateName();
    $this->assertNotEmpty($a);
    $this->assertNotSame($a, $b);
  }

  /**
   * @expectedException RuntimeException
   */
  public function testDestroyDirThrows() {
    $obj = new FilePath($this->sb);
    $this->assertSame(FilePath::TYPE_DIR, $obj->getType());
    $this->assertFileExists($obj->getPath());
    $obj->destroy();
  }

  public function testTempName() {
    $a = FilePath::tempName();
    $b = FilePath::tempName();
    $this->assertNotEmpty($a);
    $this->assertNotSame($a, $b);
  }

  public function testGetStreamHeaders() {
    $gif = new FilePath($this->sb . '/image.gif');
    $gif->put('GIF87')->save();
    $control = array(
      'Content-Type' => 'image/gif',
      'Content-Length' => 5,
    );
    $this->assertSame($control, $gif->getStreamHeaders());
  }

  public function testDirExists() {
    $file = FilePath::create($this->sb . '/temp')->parents();
    $this->assertTrue($file->exists());
  }

  public function testFileExists() {
    $file = new FilePath($this->sb . '/test.txt');
    $this->assertFalse($file->exists());
    $file->put('do')->save();
    $this->assertTrue($file->exists());
  }

  /**
   * @expectedException RuntimeException
   */
  public function testUploadWhenNotUploadThrows() {
    try {
      $dir = new FilePath($this->sb);
      $dir->put('do')->to('upload.txt')->save();
      $dir->to('copy.txt')->upload($this->sb . '/upload.txt');
    }
    catch (\Exception $exception) {
      throw $exception;
    }
  }

  public function testMove() {
    $source = new FilePath($this->sb . '/source.txt');
    $source->put('do')->save();
    $files = new FilePath($this->sb);
    $files->to('destination.txt')->move($source->getPath());
    $this->assertFileNotExists($this->sb . '/source.txt');
    $this->assertFileExists($this->sb . '/destination.txt');
    $this->assertSame('do', file_get_contents($this->sb . '/destination.txt'));

  }

  public function testCopyFrom() {
    $source = new FilePath($this->sb . '/alpha/index.html');
    $source->put('do')->save();

    $destination = new FilePath($this->sb . '/bravo');
    $destination->copyFrom($source);

    $this->assertFileExists($this->sb . '/alpha/index.html');
    $this->assertFileExists($this->sb . '/bravo/index.html');
    $this->assertSame('do', file_get_contents($this->sb . '/bravo/index.html'));
  }

  public function testCopy() {
    $source = new FilePath($this->sb . '/source.txt');
    $source->put('do')->save();
    $files = new FilePath($this->sb);
    $files->to('destination.txt')->copy($source->getPath());
    $this->assertFileExists($this->sb . '/source.txt');
    $this->assertFileExists($this->sb . '/destination.txt');
    $this->assertSame('do', file_get_contents($this->sb . '/destination.txt'));
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testPassingMoreThanFileNameFromThrows() {
    $init = new FilePath($this->sb);
    $init->from('parent/data.txt');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testPassingMoreThanFileNameToThrows() {
    $init = new FilePath($this->sb);
    $init->put('do,re,mi')->to('parent/data.txt');
  }

  public function testFromLoadGet() {
    $init = new FilePath($this->sb);
    $init->put('do,re,mi')->to('data.txt')->save();

    $load = new FilePath($this->sb);
    $list = $load->from('data.txt')->load()->get();
    $this->assertSame('do,re,mi', $list);

  }

  public function testPutJsonNoSaveGetAndGetJson() {
    $file = new FilePath($this->sb);
    $subject = array(
      'job' => 'developer',
      'name' => array(
        'first' => 'Aaron',
      ),
    );
    $json = $file->putJson($subject)->get();
    $this->assertSame(json_encode($subject), $json);
    $this->assertEquals(json_decode(json_encode($subject)), $file->getJson());
  }

  public function testPutJson() {
    $file = new FilePath($this->sb . '/test.json');
    $subject = array(
      'job' => 'developer',
      'name' => array(
        'first' => 'Aaron',
      ),
    );
    $json = $file->putJson($subject)->save()->get();
    $this->assertSame(json_encode($subject), $json);
  }

  public function testPutXmlNoSaveGetAndGetXml() {
    $file = new FilePath($this->sb);
    $subject = array(
      'day' => 'Saturday',
    );
    $subject = LoftXmlElement::fromArray($subject);
    $control = $subject->asXml();
    $xml = $file->putXml($subject)->get();
    $this->assertSame($control, $xml);
    $this->assertEquals($subject, $file->getXml());
  }

  public function testPutXml() {
    $file = new FilePath($this->sb . '/test.xml');
    $subject = array(
      'day' => 'Saturday',
    );
    $subject = LoftXmlElement::fromArray($subject);
    $control = $subject->asXml();
    $response = $file->putXml($subject)->save()->get();
    $this->assertSame($control, $response);
  }

  /**
   * @expectedException RuntimeException
   */
  public function testSaveWhenCantWriteThrows() {
    try {
      $file = new FilePath($this->sb . '/temp');
      $file->put('# Title')->to('index.md')->save();
      chmod($this->sb . '/temp', 0444);
      $file->put('# New Title')->save();
    }
    catch (\Exception $exception) {
      chmod($this->sb . '/temp', 0777);
      throw $exception;
    }
  }

  public function testSaveWithBasenameArg() {
    $file = new FilePath($this->sb);
    $this->assertFileNotExists($this->sb . '/index.md');
    $file->put('# Title')->to('index.md')->save();
    $this->assertFileExists($this->sb . '/index.md');

  }

  /**
   * @expectedException RuntimeException
   */
  public function testSaveWithoutBasenameThrows() {
    $file = new FilePath($this->sb);
    $file->put('# Title')->save();
  }

  public function testBasename() {
    $file = new FilePath($this->sb . '/test.json');
    $this->assertSame('test.json', $file->getId());
    $this->assertSame('final.json', $file->to('final.json')
      ->getId());
  }

  public function testLoad() {
    $file = new FilePath($this->sb . '/test.json');
    $this->assertSame($file, $file->put('{"json":true}')->save()->load());
    $this->assertSame('{"json":true}', $file->get());
    $this->assertEquals((object) array('json' => TRUE), $file->getJson());
  }

  public function testSaveCreatesFile() {
    $file = new FilePath($this->sb . '/dir/test.json');
    $this->assertFileNotExists($this->sb . '/dir/test.json');
    $file->put('{"json":true}')->save();
    $this->assertFileExists($this->sb . '/dir/test.json');
  }

  /**
   * Provides data for testConstructorCreatesNestedDirs.
   */
  function DataForTestConstructorCreatesNestedDirsProvider() {
    $tests = array();
    $tests[] = array('/do/re/mi');
    $tests[] = array('/do/re/mi/test.json');

    return $tests;
  }

  /**
   * @dataProvider DataForTestConstructorCreatesNestedDirsProvider
   */
  public function testConstructorDoesNotCreateNestedDirectoriesWhenInstallOptionIsFalseButInstallMethodDoes($subject) {
    $control = $this->sb . $subject;
    $this->assertFileNotExists($control);
    $obj = new FilePath($control, ['install' => FALSE]);
    $this->assertFileNotExists(dirname($control));
    $obj->parents();
    $this->assertFileExists(dirname($control));
  }

  /**
   * @dataProvider DataForTestConstructorCreatesNestedDirsProvider
   */
  public function testConstructorDoesNotCreateNestedDirectoriesWhenInstallOptionIsFalse($subject) {
    $control = $this->sb . $subject;
    $this->assertFileNotExists($control);
    new FilePath($control, ['install' => FALSE]);
    $this->assertFileNotExists(dirname($control));
  }

  /**
   * @dataProvider DataForTestConstructorCreatesNestedDirsProvider
   */
  public function testConstructorCreatesNestedDirs($subject) {
    $control = $this->sb . $subject;
    $this->assertFileNotExists($control);
    FilePath::create($control)->parents();
    $this->assertFileExists(dirname($control));
  }

  public function setUp() {
    $this->createSandbox();
    $this->assertTrue(is_dir($this->sb));
  }

  public function tearDown() {
    $this->destroySandbox();
  }
}

