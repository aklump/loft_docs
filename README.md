[markdown]:http://daringfireball.net/projects/markdown/
[help_module]:http://drupal.org/project/advanced_help
[codekit]:http://incident57.com/codekit/
[lynx]:http://lynx.isc.org/
For installation instructions scroll to the bottom.

##What Is This?
This tool provides one central place to manage your documentation allowing you to compose in Markdown and have the benefits of all of the following output formats:

1. HTML
2. Plaintext
3. Advanced Help for Drupal

Gone are the days of having to update all your different documentation locations!

## As a Reader
1. To read documentation you probably just want to load `/html/index.html` in a browser and proceed from there.
2. Plaintext documentation may also be available in `/text/`

## As a Content Manager
1. You will need to read about compiling below; this is the step needed to generate derivative documentation from `/source`.

## As an Author
1. You will concern yourself with the `/source` directory, creating your source markdown files here.  This is the source of all documentation.

2. Only files in the `source` directory should  be edited.  All other files get created during compiling.

3. Images should e added to `source/images`.

4. Use relative links when linking to other pages inside `source`.

5. Use absolute links when linking to anything outside of `source`.

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

Output of `.txt` files requires that [Lynx][lynx] be installed.

Compiling relies on the [Code Kit app][codekit]; you should add this directory as a project before executing `compile.sh`.  The first time you compile you may need to manually compile the `.kit` files from the CodeKit UI.

Each time you want to update your documentation files, after modifying files in `source` you need to execute `compile.sh` form bash:

    ./core/compile.sh
    
## Installation
1. Download the [Markdown Perl Binary][markdown] and add it as `/core/Markdown.pl`
1. Add the root directory of this folder as a project in [Code Kit app][codekit].
1. Run the compile command:

        ./core/compile.sh

2. The necessary dirs will be created including the configuration file:

        core-config.sh  
  
1. Open and edit `core-config.sh`. **You should not delete this file once it's been created, as it is the flag that installation has taken place!** Compiling without this file may lead to some/all of your files being deleted.
2. Enter the name of the drupal module this will be used for, if applicable.
3. Enter the credentials for the drupal site if using iframes.
4. Override the php path if needed; php must have the curl library installed.
5. Run `./core/compile.sh` once more to update the configuration.
5. Test the installation by visiting `html/index.html` in a browser.
7. Installation is complete; you may now begin documenting in `source`. You most likely should first delete the example files in `source`.

### Quick Installing Lynx on Mac
Here's a quick way to get Lynx on a mac...

1. Download this application [http://habilis.net/lynxlet/](http://habilis.net/lynxlet/)
2. In shell type `cd /usr/bin`
3. Followed by `sudo ln -s /Applications/Lynxlet.app/Contents/Resources/lynx/bin/lynx`
4. Test your installation with this command `lynx`; you should see the lynx browser show up.
 
   
## Theming
The files in `/core/tpl` controls the output of the `.html` files found in `html`.  You should never modify these files, nor any files in `core`.  Instead to override the theming you should copy `core/tpl` up one directory into the base directory and override those files.

    cp -R core/tpl .
    
For css changes you should edit `/tpl/style.css` in the newly created `/tpl` file.
    

## Core Update
This script provides a self updating feature.  To update the core files, go into core and execute `update.sh`

    ./core/update.sh
    
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