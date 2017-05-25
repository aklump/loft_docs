# Changelog

## 0.9.10

* Added `Deny from All` to the .htaccess for public_html pattern.  If this breaks your compile, use a post compile hook to overwrite the default `.htaccess` file.

## 0.9

* Removed support for kit templates. Now using Twig templates.
* Lost iframe support; plans to bring it back in future.

## 0.8.18

* Removed all traces of doxygene due to no plans for implementation.

## 0.8.10

* API CHANGE: `auto-generated.outline.json` is now called `outline.auto.json`

## 0.8

* BREAKING CHANGE: The paths in the config var _README_ are now relative to the directory that contains `core-config.sh`, not the `source` folder.

