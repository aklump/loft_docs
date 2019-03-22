# Changelog

## 0.11

* Added [internal linking](@linking).
* Changed the output width from 1024px to 800px.  To go back you need to [custom theme](@theming) your docs.

## 0.10

* Reduced default polling for watch.php from 20 to 2.
* Removed the `sort` key from the json outlines.
* Added support for includes for files ending in _.twig.md_.
* Todos are only compiled if you enable them in _outline.merge.json_.
* Todos are now present as an include file, not as a page.  Therefore you must both enable aggregation and use an include expression in a file.  See documentation for more info.

## 0.9.10

* POSSIBLE BREAKING CHANGE: Added `Deny from All` to the `.htaccess` for public_html pattern.  If this breaks your compile, use a post compile hook to overwrite the default `.htaccess` file; see `hooks/htaccess.sh` for an example of how to do this.  Be sure to register the hook file in `core-config.sh` in the `post_hooks` section, or it won't be called.

## 0.9

* Removed support for kit templates. Now using Twig templates.
* Lost iframe support; plans to bring it back in future.

## 0.8.18

* Removed all traces of doxygene due to no plans for implementation.

## 0.8.10

* API CHANGE: `auto-generated.outline.json` is now called `outline.auto.json`

## 0.8

* BREAKING CHANGE: The paths in the config var _README_ are now relative to the directory that contains `core-config.sh`, not the `source` folder.

