# How to install

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

# How to install Lynx on Mac

Here's a quick way to get Lynx on a mac...

1. Download this application [http://habilis.net/lynxlet/](http://habilis.net/lynxlet/)
2. In shell type `cd /usr/bin`
3. Followed by `sudo ln -s /Applications/Lynxlet.app/Contents/Resources/lynx/bin/lynx`
4. Test your installation with this command `lynx`; you should see the lynx browser show up.

## or with homebrew

1. `brew install lynx`
