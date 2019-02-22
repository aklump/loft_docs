# Documentation Version

If you are documenting software, you are most likely going to want your documentation to follow the same version as the software.  The default theme writes the version in the footer.  This page tells you how to indicate the version to the compiler.

If you do nothing the default version is always `1.0`.

## Point to a Static File

In _core-config.sh_ you can point to a static file containing a version string.  Several file are understood including:

* _composer.json_
* _package.json_
* _\*.info_

Add the following to _core-config.sh_:

    version_file = "/some/absolute/path/version.info"

or a relative file (relative to the directory containing _core-config.sh_)

    version_file = "../composer.json"

## Point to a Script

In the event `version_file` is not sufficient you can point to a script that echos the version string of your documentation_.  These version hook script (PHP or BASH) receives the same arguments as the pre/post hooks.  You define your version hook in config.  See `version_hook.php` as an implementation example.  Only one file is allowed in the declaration; either php or shell.

    version_hook = "version_hook.php"
