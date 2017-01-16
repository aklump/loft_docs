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
| $argv[4] | $4      | The absolute filepath to the root_dir directory  |

## Generating Content
Hooks/plugins should not generate content to /source directory as this will affect the watcher, instead create any files needed into the core/cache/source.
