[markdown]:http://daringfireball.net/projects/markdown/
[markdown_php]:http://michelf.ca/projects/php-markdown/
[help_module]:http://drupal.org/project/advanced_help
[codekit]:http://incident57.com/codekit/
[lynx]:http://lynx.isc.org/

##What Is Loft Docs?
**Loft Docs is the last project documentation tool you'll ever need.**  Loft Docs provides one central place to manage your documentation allowing you to compose in Markdown and have the benefits of simultaneous output to the following formats:

1. An indexed, multi-page website
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


## Todo Items/Tasklist
Todo item aggregation is a neat feature that works thus.  Add one or more todo items to your markdown source files using the [following syntax](https://github.com/blog/1375-task-lists-in-gfm-issues-pulls-comments) and when you compile, your todo items will be aggregated and sorted into a new markdown file called `_tasklist.md`.  **Do not alter the content of `_tasklist.md` or you will loose your work.**

When items are aggregated, the filenames are prepended to the todo item.  The final list will be filtered for uniqueness, and duplicates removed.  If the same todo item appears more than once in a file, it will be reduced to a single item; but the same todo item can appear in more than one file, since the filename prepend creates uniqueness.

    - [ ] a task list item

### Sorting todo items
Use the weight flag `@w` followed by an int or float number indicating the sort weight.  Lower numbers appear first.  Sorting will happen only on the aggregated tasklist, not in the indiviual source files.

    - [ ] a task list item @w-10
    - [ ] a task list item @w10
    - [ ] a task list item @w10.1

### help.ini
Make sure to add something like this to `help.ini` so your tasklist will be indexed:

    [_tasklist]
    title = "My Tasklist"

### Altering the filename of the todo list
Add something like this line to your config file

    # path to the tasklist file relative to source/
    todos = '_different_tasklist_name.md'


### iFrames
One of the cool features is that compiling will grab iframe source and render it directly into the html for offline viewing.  The way to do this is just to include an `iframe` tag in your source code like so:

    <iframe src="http://www.my-site.com/admin/iframe/content" width="100%" height="100%"></iframe>

Then during compiling, the iframe source will be grabbed and then inserted as an html snippet in the place of the `iframe` tag.

#### Behind a Drupal Login
In some cases, your iframe content may be behind a Drupal login.  There is a contingency for this and it involves using the correct settings in `core-config.sh`.  You need to add or uncomment the following, replacing the credentials as appropriate.  That way the compiler will try to log in to your drupal site first before visiting the iframe source url.
    
    credentials = "http://user:pass@www.my-site.com/user/login";

## Use with Drupal
Compiling will output files compatible with the [Advanced Help Module for Drupal][help_module].  By default these files will output to a folder named `advanced_help`, but with a little configuration the folder will output directory to the root of your module folder as `help`.

This will also output `README.txt` directly to your module's root directory, so long as you create `/source/README.md` and make the settings shown below.

Follow these steps if you are using this for documenting a Drupal module:

1. Place this tool in the root of your module directory.  Add it to `.gitignore` so it doesn't get added to your module's repository.
3. Make sure the folder `help` does not exist in your module's root; if it does it will be erased!
4. Make sure `README.txt` does not exist in your module's root; if so it too will be erased during compiling.
1. Make sure the following settings are present in `core-config.sh`; replace `my_module` with the name of the module
        
        drupal_module = 'my_module';
        drupal_dir = '../help'
        README = '../README.txt'
        
2. Make sure to create `/source/README.md`; this compiles to `README.txt`.
3. You must also create the advanced help .ini file in `source/help.ini`.  **Please note that you should omit the module name!**  Just name it `help.ini`.  See this file `advanced_help/help/using-advanced-help.html` inside the [Advanced Help Module][help_module] for more info.

After you've written you markdown files, and compiled, you will see the `help` directory and the `README.txt` files in the root of your module.

### Links
Do not use `&topic:module/topic&` as is suggested, rather follow the instructions above and the Advanced Help topic links will automatically be generated for you.

### Images
Do not use the `&path&` convention when linking to images (as is recommended by the advanced help module), this convention will automatically be added for you during compiling.  Rather, do relative linking like this:

**Correct:**

    <img src="images/my_diagram.png" />

**In-Correct:**

    <img src="&path&images/my_diagram.png" />

<a name="compiling"></a>
## Compiling
After a round of changes to the files found in `/source`, you will need to export or _compile_ your documentation.

### How to compile
Each time you want to update your documentation files, after modifying files in `source` you need to execute `compile.sh` from bash, make sure you are in the root directory of this package.

    ./core/compile.sh

### Defining the documentation version
Some of the templates utilize a version string.  How this is provided is the the next topic covered.

**By default, Loft Docs will look for `*.info`, two directories above `core/`.**



The templates receive a version string, which is always "1.0" unless you implement a version hook--_a php or shell script that echos the version string of your documentation_.  These version hook script receives the same arguments as the pre/post hooks.  You define your version hook in config.  See `version_hook.php` as an implementation example.  Only one file is allowed in the declaration; either php or shell.

    version_hook = "version_hook.php"

### Pre/Post hooks
You may specify one or more php or shell scripts to be called both before and/or after compiling using the `pre_hooks` and `post_hooks` config options.  The paths you present are relative to the root directory, a.k.a. the directory which contains `core/`.  Compilation pauses until each script is finished.

    pre_hooks = "pre_compile.sh pre_compile.php"
    post_hooks = "post_compile.sh post_compile.php"

The scripts will receive the following arguments:

| php arg | bash arg | description                                      |
|----------|---------|--------------------------------------------------|
| $argv[1] | $1      | The absolute filepath to the `source/` directory |
| $argv[2] | $2      | The absolute filepath to the `core/` directory   |

<a name="install"></a>
## Installation
How you incorporate Loft Docs is up to you, but there are two scenarios which will be described here, with suggested installation instructions.

### Stand-alone implementation
If your goal is simply to document something, and these files will not integrate into another larger project (think git repo), then this is a stand-alone installation.  This would also be the case where you're using Loft Docs to build a website.  Loft Docs' root is the root of your project.  Here's the minimum file structure of a stand-alone implementation:

    /.gitignore
    /core
    /core-config.sh
    /core-version.info
    /public_html
    /source
    /stand_alone.info

In this scenario the version string of your project is contained in `/stand_alone.info` which is one level above Loft Docs' core, and so your config file would contain this line:

    version_file = "../web_package.info"

Or, for greater flexibility (so long as you've only one `.info` file), it could be:

    version_file = "../*.info"

If you were to host this as a website, `public_html` is your web root.    

### Integrated implementation
If you are installing Loft Docs _inside_ the existing code of a larger project, then this constitutes an integrated installation.  Loft Docs is not the root of the larger project, but a sub-folder, maybe you call it `docs` and store it in the root of the other project.

    /docs/core
    /other_project_file1
    /other_project_file2
    /other_project_file3
    /web_package.info

In this scenario the version string of your project is contained in `/web_package.info` which is two levels above Loft Docs' core, and so your config file would contain this line:

    version_file = "../../web_package.info"

Or, for greater flexibility (so long as you've only one `.info` file), it could be:

    version_file = "../../*.info"

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
 
<a name="theming"></a>   
## Theming
The files in `/core/tpl` control the output of the `.html` files found in the website folder `public_html`.  You should never modify these files, nor any files in `core`.  Instead to override the theming you should copy `core/tpl` up one directory into the base directory and override those files.

    cp -R core/tpl .
    
For css changes you should edit `/tpl/style.css` in the newly created `/tpl` file.
    

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