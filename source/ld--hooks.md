---
title: Using pre/post hooks
tags: extending pluggable
---
# Pre/Post hooks

You may specify one or more php or shell scripts to be called both before and/or after compiling using the `pre_hooks` and `post_hooks` config options.  The paths you present are relative to `{root}/hooks`.  Compilation pauses until each script is finished.

    pre_hooks = "pre_compile.sh pre_compile.php"
    post_hooks = "post_compile.sh post_compile.php"

**Scripts must be located in `{root}/hooks`.**

The scripts will receive the following arguments:

| php arg | bash arg | description                                      |
|----------|---------|--------------------------------------------------|
| $argv[1] | $1      | The absolute filepath to the `source/` directory |
| $argv[2] | $2      | The absolute filepath to the `core/` directory   |
| $argv[3] | $3      | The absolute filepath to the version file        |
| $argv[4] | $4      | The absolute filepath to the parent directory of `source`, a.k.a the root directory  |
| $argv[5] | $5      | The absolute filepath to the compiled website directory  |
| $argv[6] | $6      | The absolute filepath to the compiled html directory  |
| $argv[7] | $7      | The absolute filepath to the compiled text directory  |
| $argv[8] | $8      | The absolute filepath to the compiled drupal directory  |
| $argv[9] | $9      | The absolute filepath to write dynamic pages before compile |
| $argv[10] | $10    | The absolute filepath to write dynamic twig includes to.  A file you create here is available using `{% include('my_dynamic_file.md') %}` |

## Generating Content

Hooks/plugins MUST NEVER create files in _/source_ as this will affect the watcher, instead create any files in `$argv[9]`.
## Output

You may print or echo from your script and it will be echoed to the user.

## Using Twig Templates for Generated Content

A common pre hook concern is to generate dynamic pages.  If you do this with a php file, you can have access to [Twig](https://twig.symfony.com/doc/2.x) via the core dependencies.  

If your template file is located in source, it should use a .twig extension.  Then in your hook, spit the compiled out as .md.

Here is an example scaffold for a pre hook that uses a twig template to create a page.

### hooks/plugins_dir.php

    <?php
    /**
     * @file Generate the plugins directory page by scanning the plugins directory.
     */
    
    // Load the core autoload, which will give access to Twig
    require_once $argv[2] . '/vendor/autoload.php';
    
    // ...
    // Create an array $vars from some process
    // ...
    
    $loader = new Twig_Loader_Filesystem(dirname(__FILE__));
    $twig = new Twig_Environment($loader);
    
    // Template file is located in /hooks as well.
    $template = $twig->load('plugins_dir.md');
    
    // Write the file using $argv[9] to the correct compilation directory.
    file_put_contents($argv[9] . '/plugins_dir.md', $template->render($vars));

### hooks/plugins_dir.md

    ---
    ---
    # Plugin Library
    
    {% for plugin in plugins %}
    ## {{ plugin.name }}
    
    > {{ plugin.description }}
    
    {{ plugin.readme }}
    
    ### Usage Example
    
    <pre>{{ plugin.example }}</pre>
    
    {% endfor %}

