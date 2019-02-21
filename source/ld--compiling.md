# Compiling

After a round of changes to the files found in `/source`, you will need to export or _compile_ your documentation.

## Requirements

1. In order to use the text format you must have [Lynx](http://lynx.isc.org/) installed.

## How to compile

Each time you want to update your documentation files, after modifying files in `source` you need to execute `compile` from bash, make sure you are in the root directory of this package.

    ./core/compile

## Folders and Other Includes

By default folders in the `source` file are copied to the compiled output directories.  In some cases you will want greater control and/or you will not want to mess up `source` with boilerplate folders.  For this reason there is a means to include files/folders in the compiled output by creating a second directory, which is merged into to your final build.  Place your boilerplate content there.

For example to include a js directory in the website output you would create that folder as `compile/public_html/js`.

    source/
        about.md
    compile/
        public_html/
            js/
                app.js
                extra.js

If you've reconfigured the folder name for the website directory, make sure to match the folder structure to the reconfigured name.

## Defining the documentation version

Some of the templates utilize a version string.  How this is provided is the the next topic covered.

If no version can be found the string will always be 1.0

**By default, Loft Docs will look for `*.info`, in the directory above `core/`.**  If this is not working or desired then you can specify a path in _core-config.sh_ as such:

    version_file = "/some/absolute/path/version.info"

There is a built in a version hook that can sniff a version from .info and .json files and that may suffice.  If not read on about a custom version hook...

_A version hook is a php or shell script that echos the version string of your documentation_.  These version hook script receives the same arguments as the pre/post hooks.  You define your version hook in config.  See `version_hook.php` as an implementation example.  Only one file is allowed in the declaration; either php or shell.

    version_hook = "version_hook.php"

## Removing Compiled Files

You may delete all compiled files using the _clean_ command.

    ./core/clean
