# Compiling

After a round of changes to the files found in `/source`, you will need to export or _compile_ your documentation.

## How to compile

Each time you want to update your documentation files, after modifying files in `source` you need to execute `compile` from bash, make sure you are in the root directory of this package.

```shell
./core/compile
```

You may indicate an alternative config file by passing it as the first argument, e.g. 

```shell
./core/compile "/Users/aklump/Code/Projects/InTheLoftStudios/D8Shorts/site/dist/documentation/core-config.sh"
```

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

## Removing Compiled Files

You may delete all compiled files using the _clean_ command.

    ./core/clean

## Auto-Compiling / File Watching

To watch your source directory for file changes and compile automatically as you work, you can use:

    ./core/watch
