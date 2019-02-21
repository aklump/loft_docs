# FilePath: A Better Way to Work With Files and Folders

## Working with Directories

### Create a directory and all parents

Either of these will work.  The second, though, returns a new instance object.

    FilePath::ensureDir('foo/bar/baz');
    $fp = FilePath::create('foo/bar/baz')->parents();

### Create a hidden directory

In this case you have to bypass the automatic determination of a file or directory based on the format of the path, so instead be explicit that you want a directory.

    list($path_to_dir) = FilePath::ensureDir('foo/bar/.baz', NULL, TRUE);
    $hidden_dir_object = FilePath::create('foo/bar/.baz', NULL, [
      'type' => FilePath::TYPE_DIR,
    ])->parents();

### List files and folders

    $dir = FilePath::create('foo/bar/baz');
    $filesAndFolders = $dir->children()->all();
    $files = $dir->children()->justFiles()->all();
    $dirs = $dir->children()->justDirs()->all();

### List files and folders recursively

    $dir = FilePath::create('foo/bar/baz');
    $filesAndFolders = $dir->descendents()->all();
    $files = $dir->descendents()->justFiles()->all();
    $dirs = $dir->descendents()->justDirs()->all();

### List recursively, limit to two levels

    $dir = FilePath::create('foo/bar/baz');
    $filesAndFolders = $dir->descendents(2)->all();
    $files = $dir->descendents(2)->justFiles()->all();
    $dirs = $dir->descendents(2)->justDirs()->all();
    
### List pdf files recursively

    $dir = FilePath::create('foo/bar/baz');
    $files = $dir->descendents('/\.pdf$/')->justFiles()->all();

### List pdf files recursively limit to 1 level

    $dir = FilePath::create('foo/bar/baz');
    
    // Notice the order of the limit level doesn't matter.
    $files = $dir->descendents('/\.pdf$/', 1)->justFiles()->all();
    $files = $dir->descendents(1, '/\.pdf$/')->justFiles()->all();
    
    // or just us children()
    $files = $dir->children('/\.pdf$/')->justFiles()->all();

### List all but pdf files recursively

    $dir = FilePath::create('foo/bar/baz');
    
    // When two strings are sent as arguments, the first is use to match and the second to exclude; empty strings are ignored.
    $files = $dir->descendents('', '/\.pdf$/')->justFiles()->all();

### List any file matching foo unless it ends with .txt

    $dir = FilePath::create('foo/bar/baz');
    $files = $dir->descendents('/foo/', '/\.txt/')->justFiles()->all();


## Working with Files

When you instantiate with a filepath, the file will not be created, but all the parent directories will be created automatically if you attempt to write to the filepath.  If you do not want this, you must use the `parents = false` option to the constructor. 

### Attributes of a file

    $f = FilePath::create('/parent/dir/hello_world.txt');
    
    ...
    
    $path = $f->getPath();
    $extension = $f->getExtension();
    $fname = $f->getFileName();
    $basename = $f->getBaseName();
    $basename = $f->getId();
    $directory = $f->getDirName();
    $mime = $f->getMimeType();
    $hash = $f->getHash();

### Create Empty Files

    // The parent $dir into which you will create files.
    $dir = FilePath::create('/parent/dir/ect/ory');
    
    $file1 = $dir->to('do.txt')->save();
    $file2 = $dir->to('re.txt')->save();
    $file3 = $dir->to('mi.txt')->save();

### Create Many Files With Same Content

    // $dir is not written to disk, but exists as a "stamp" or "template".
    $dir = FilePath::create('/parent/dir/ect/ory');
    $dir->put('lorem ipsum');
    
    // The save method will "apply the stamp" to disc with the contents of the stamp.
    $file1 = $dir->to('do.txt')->save();
    $file2 = $dir->to('re.txt')->save();
    $file3 = $dir->to('mi.txt')->save();

### Create Many Files With Different Content

    $dir = FilePath::create('/parent/dir/ect/ory');
    
    $file1 = $dir->put('lorem')->to('do.txt')->save();
    $file2 = $dir->put('ipsum')->to('re.txt')->save();
    $file3 = $dir->put('dolar')->to('mi.txt')->save();

### Writing to a File

    $file = FilePath::create('/parent/dir/hello_world.txt');
    $path = $file->put('hello world')->save()->getPath();
    
    // The contents of the file are already in memory so load() is not needed.
    $contents = $file->get();
    
### Reading a File
    
    $contents = FilePath::create('/parent/dir/hello_world.txt')
      ->load()
      ->get();
    
By passing a directory and an extension, a temporary filename will be created automatically.

### Working with a time-based name file

    $file = FilePath::create('/foo/bar/baz/' . FilePath::dateName('json'));
    //  $file->getPath() === /foo/bar/baz/2017-11-17T00-55-20Z.json

### Delete a file

    $file = FilePath::create('/foo/bar.txt')->destroy();

## Copying Files

### Copy a file

    $source = FilePath::create(ROOT . '/quick_start.json');
    $destination = FilePath::create(getcwd() . '/sitemap.json')
      ->copyFrom($source);

### Copy a file from one directory to another

    $destination_dir = FilePath::create('/dir/where/file/end/up');
    $destination_dir->copyFrom('/source/file.md');
    
    $another_file = FilePath::create('/source2/file2.txt');
    $destination_dir->copyFrom($another_file);

### Move a file from one directory to another

    $destination_dir = FilePath::create('/dir/where/file/end/up');
    $destination_dir->moveFrom('/source/file.md');

### Duplicate a file within same directory

You must give the new name for the duplicated file.

    $file_to_duplicate = FilePath::create('/some/file/original.md');
    $file_to_duplicate->to('clone.md')->copy('original.md');
