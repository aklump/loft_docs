---
title: Overview of Loft Docs
sort: -100
---
[markdown]:http://daringfireball.net/projects/markdown/
[markdown_php]:http://michelf.ca/projects/php-markdown/
[codekit]:http://incident57.com/codekit/
[lynx]:http://lynx.isc.org/

#What Is Loft Docs?
**Loft Docs is the last project documentation tool you'll ever need.**  Loft Docs provides one central place to manage your documentation allowing you to compose in Markdown and have the benefits of simultaneous output to the following formats:

1. An indexed, multi-page, stand-alone, searchable (via js) website
2. HTML
3. Plaintext
4. MediaWiki
5. Advanced Help for Drupal

Gone are the days of having to update all your different documentation locations!

_For installation instructions [scroll down](#install)._

## Features
1. Tasklist todo item aggregation and sorting.
2. Output to many popular formats.
3. Compilation hooks for before and after.
4. Custom [website theming](#theming).
5. Searchable pages (in the website output)

## As a Reader
1. To read documentation you probably just want to load `public_html/index.html` in a browser and proceed from there.
2. Plaintext documentation may also be available in `text/`.
3. MediaWiki documentation if supported will be found in `mediawiki/`.

## As an Author
1. You will concern yourself with the `/source` directory, creating your source markdown files here.  This is the source of all documentation.

2. Only files in the `source` directory should  be edited.  All other files get created during compiling.

3. Images can be added to `source/images`.

4. Use relative links when linking to other pages inside `source`.

5. Use absolute links when linking to anything outside of `source`.


## As an Admin/Content Manager
1. You will need to read about [compiling](#compiling) below; this is the step needed to generate derivative documentation from `/source`.

### Linking to Other Help Pages
You should do the following to link internally to `source/page2.html`

    <a href="page2.html">Link to Next Page</a>

## As a Developer
If you are implementing any hooks and you need component or include files, which compile to markdown files in `/source`:

1. Put these component files in `/parts` not in `/source`.
1. Make sure the generated files begin with the underscore, e.g., `_my_compiled_file.md`.  That will indicate these files are compiled and can be deleted using `core/clean.sh`.

<a name="compiling"></a>
## Compiling
After a round of changes to the files found in `/source`, you will need to export or _compile_ your documentation.

### How to compile
Each time you want to update your documentation files, after modifying files in `source` you need to execute `compile.sh` from bash, make sure you are in the root directory of this package.

    ./core/compile.sh

### Defining the documentation version
Some of the templates utilize a version string.  How this is provided is the the next topic covered.

If no version can be found the string will always be 1.0

**By default, Loft Docs will look for `*.info`, in the directory above `core/`.**  If this is not working or desired then you can specify a path in _core-config.sh_ as such:

    version_file = "/some/absolute/path/version.info"

There is a built in a version hook that can sniff a version from .info and .json files and that may suffice.  If not read on about a custom version hook...

_A version hook is a php or shell script that echos the version string of your documentation_.  These version hook script receives the same arguments as the pre/post hooks.  You define your version hook in config.  See `version_hook.php` as an implementation example.  Only one file is allowed in the declaration; either php or shell.

    version_hook = "version_hook.php"

### Removing Compiled Files
You may delete all compiled files using the _clean_ command.

    ./core/clean.sh


### Requirements
1. Compiling uses [Markdown Php][markdown_php], which is included in this distribution.
1. Output of `.txt` files requires that [Lynx][lynx] be installed.

### How to install
1. Run the compile command, the first time it is runned, installation takes place.

        ./core/compile.sh

2. The necessary dirs will be created including these configuration file(s):

        core-config.sh
  
1. Open and edit `core-config.sh`. **You should not delete this file once it's been created, as it is the flag that installation has taken place!** Compiling without this file may lead to some/all of your files being deleted.
2. Enter the name of the drupal module this will be used for, if applicable.
3. Enter the credentials for the drupal site if using iframes.
4. Override the php path if needed; php must have the curl library installed.
5. Run `./core/compile.sh` once more to update the configuration.
5. Test the installation by visiting `public_html/index.html` in a browser, this is the webpage output and should show you a few example pages.
7. Installation is complete; you may now begin documenting in `source`. You most likely should first delete the example files in `source`.

### How to install Lynx on Mac
Here's a quick way to get Lynx on a mac...

1. Download this application [http://habilis.net/lynxlet/](http://habilis.net/lynxlet/)
2. In shell type `cd /usr/bin`
3. Followed by `sudo ln -s /Applications/Lynxlet.app/Contents/Resources/lynx/bin/lynx`
4. Test your installation with this command `lynx`; you should see the lynx browser show up.
 
#### or with homebrew
1. `brew install lynx`

## Core update
Loft Docs provides a core update feature as seen below.  From the root directory type:

    ./core/update.sh
    
## Rationale
The rationalle behind this project is that it is easy to write markdown files, and it is easy to share a static html based documentation file, and it is easy to use Drupal Advanced Help module, and it is easy to version your documentation in git; but to do all this together at once… was NOT EASY.

But now with _Loft Docs_... it's easy.

##Contact
* **In the Loft Studios**
* Aaron Klump - Developer
* PO Box 29294 Bellingham, WA 98228-1294
* _aim_: theloft101
* _skype_: intheloftstudios
* _d.o_: aklump
* <http://www.InTheLoftStudios.com>
