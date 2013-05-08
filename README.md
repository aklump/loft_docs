[markdown]:http://daringfireball.net/projects/markdown/
[help_module]:http://drupal.org/project/advanced_help
[codekit]:http://incident57.com/codekit/
For installation instructions scroll to the bottom.

## Readers
Offline readers should only concern themselves only with the `html` folder.  Load the `index.html` file to get started.

## Authors
Authors should add/edit the markdown files as these are considered the source files.

Only files in the `source` directory should  be edited.  All other files get created when you compile. 

Images should e added to `source/images`.

Use relative links when linking to other pages inside `source`.

Use absolute links when linking to anything outside of `source`.

### Linking to Other Help Pages
You should do the following to link internally to `source/page2.html`

    <a href="page2.html">Link to Next Page</a>

### iFrames
One of the cool features is that compiling will grab iframe source and render it directly into the html for offline viewing.  @todo flesh this out.    

## Advanced Help Drupal
Compiling will output files compatible with the [Advanced Help Module for Drupal][help_module].

Place this entire directory inside your module directory and create a symlink to the advanced help folder.

    ln -s [this dir]/advanced_help help

You must also create the advanced help .ini file in `source/help.ini`.  **Please note that you should omit the module name!**  Just name it `help.ini`.  See this file `advanced_help/help/using-advanced-help.html` inside the [Advanced Help Module][help_module] for more info.

After you've written you markdown files, you need to compile to create the `advanced_help` directory.

### Links
Do not use `&topic:module/topic&` as is suggested, rather follow the instructions above and the Advanced Help topic links will automatically be generated for you.

### Images
Do not use the `&path&` convention when linking to images (as is recommended by the advanced help module), this convention will automatically be added for you during compiling.  Rather, do relative linking like this:

**Correct:**

    <img src="images/my_diagram.png" />

**In-Correct:**

    <img src="&path&images/my_diagram.png" />


## Compiling
### Requirements
Compiling requires the [Markdown Perl Binary][markdown], add it to the `core/Markdown.pl`.    

Compiling relies on the [Code Kit app][codekit]; you should add this directory as a project before executing `compile.sh`.  The first time you compile you may need to manually compile the `.kit` files from the CodeKit UI.

Each time you want to update your documentation files, after modifying files in `source` you need to execute `compile.sh` form bash:

    . compile.sh
    
## Installation
1. First run, execute `. compile` to install the source directory and config file.
1. Edit `config`.
2. Enter the name of the drupal module this will be used for, if applicable
3. Enter the credentials for the drupal site if using iframes
4. Override the php path if needed; php must have the curl library installed

If you try compile immediately upon installing, an example configuration will be installed at `source`.  If you wish to bypass the example, then simply create a `source dir` and begin writing your markdown:

    mkdir source
    
## Theming
The file in `tpl` controls the output of the `.html` files found in `html`.  You should never modify these files, nor any files in `core`.  Instead to override the theming you should copy `core/tpl` up one directory into the base directory and override those files.

    cp -R core/tpl tpl
    
## Rationalle
The rationalle behind this project is that it is easy to write markdown files, and it is easy to share a static html based documentation file, and it is easy to use Drupal Advanced Help module, and it is easy to verison your documentation in git; but to do all this together at once… was NOT EASY.

So this project is born to satisfy this need.

##Contact
* **In the Loft Studios**
* Aaron Klump - Developer
* PO Box 29294 Bellingham, WA 98228-1294
* _aim_: theloft101
* _skype_: intheloftstudios
* _d.o_: aklump
* <http://www.InTheLoftStudios.com>