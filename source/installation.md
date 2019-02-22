# How to install

1. Run the compile command, the first time it is runned, installation takes place.

        ./core/compile

2. The necessary dirs will be created including these configuration file(s):

        core-config.sh

1. Open and edit `core-config.sh`. **You should not delete this file once it's been created, as it is the flag that installation has taken place!** Compiling without this file may lead to some/all of your files being deleted.
2. Enter the name of the drupal module this will be used for, if applicable.
3. Enter the credentials for the drupal site if using iframes.
4. Override the php path if needed; php must have the curl library installed.
5. Run `./core/compile` once more to update the configuration.
5. Test the installation by visiting `public_html/index.html` in a browser, this is the webpage output and should show you a few example pages.
7. Installation is complete; you may now begin documenting in `source`. You most likely should first delete the example files in `source`.

# How to install Lynx on Mac

## Requirements

1. 

Here's a quick way to get Lynx on a mac...

1. Download this application [http://habilis.net/lynxlet/](http://habilis.net/lynxlet/)
2. In shell type `cd /usr/bin`
3. Followed by `sudo ln -s /Applications/Lynxlet.app/Contents/Resources/lynx/bin/lynx`
4. Test your installation with this command `lynx`; you should see the lynx browser show up.

## or with homebrew

1. `brew install lynx`

---

## Installation Part 2

How you incorporate Loft Docs is up to you, but there are two scenarios which will be described here, with suggested installation instructions.

## Quick Start

1. Copy the contents of `/dist` into your project.
1. From within `dist`, in the shell, run `./core/compile`.
1. Open `dist/public_html/welcome.html` and follow instructions.

## Stand-alone implementation

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

## Integrated implementation

If you are installing Loft Docs _inside_ the existing code of a larger project, then this constitutes an integrated installation.  Loft Docs is not the root of the larger project, but a sub-folder, maybe you call it `docs` and store it in the root of the other project.

    /docs/core
    /other_project_file1
    /other_project_file2
    /other_project_file3
    /web_package.info

In this scenario the version string of your project is contained in `/web_package.info` which is one levels above Loft Docs, and so your config file would contain this line:

    version_file = "../web_package.info"

Or, for greater flexibility (so long as you've only one `.info` file), it could be:

    version_file = "../*.info"
