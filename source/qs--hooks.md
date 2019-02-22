---
title: Hooks
tags: extending pluggable
---
# Pre/Post hooks

* Create your hook file in _hooks_, which is a sibling directory to _source_.
* Enable the hook file in _core.config.sh_, e.g., `pre_hooks = "pre_compile.php"`

You may specify one or more PHP or shell scripts to be called both before and/or after compiling using the `pre_hooks` and `post_hooks` config options.  The paths you present are relative to `{root}/hooks`.  Compilation pauses until each script is finished.

    pre_hooks = "pre_compile.sh pre_compile.php"
    post_hooks = "post_compile.sh post_compile.php"

## Arguments Sent to Scripts

The scripts will receive the following arguments:

| php arg | bash arg | description                                      |
|----------|---------|--------------------------------------------------|
| $argv[1] | $1      | Path to the `source/` directory |
| $argv[2] | $2      | Path to the `core/` directory   |
| $argv[3] | $3      | Path to the version file        |
| $argv[4] | $4      | Path to the parent directory of `source`, a.k.a the root directory  |
| $argv[5] | $5      | Path to the compiled website directory  |
| $argv[6] | $6      | Path to the compiled html directory  |
| $argv[7] | $7      | Path to the compiled text directory  |
| $argv[8] | $8      | Path to the compiled drupal directory  |
| $argv[9] | $9      | Path to write dynamic pages and includes before compile |
| $argv[10] | $10    | Path to the outline JSON file |

## Generating Dynamic Content

Portions of your pages that need to be computed are generally going to be dynamic includes, which are included on your static pages named ending with `.twig.md`.

### PHP Hooks

You have access to an instance of `$compiler`, which has most methods you'd need.  Also any core classes are available and autoloaded so you simple need to declare them via `use` statements at the top of your hook file, e.g.,

    <?php
    
    use AKlump\LoftDocs\PhpClassMethodReader;
    ...

PHP hook files should only save content using a method on the `$compiler` instance.  To save an include file, use:

    $compiler->addInclude(...
    
If you want to generate an entire dynamic page, you should use:
    
    $compiler->addSourceFile(...
    
Be sure to check out these classes for help with dynamic content:

* `\AKlump\LoftLib\Code\Markdown`      
* `\AKlump\LoftDocs\PhpClassMethodReader`      

### BASH Hooks
    
Dynamice files should be written to the directory defined in `$9`    

## Screen Output

You may print or echo from your script and it will be echoed to the user.
