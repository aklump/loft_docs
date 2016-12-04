# Table of Contents/Indexing
The index of your documentation may be provided in three ways: two are explicit and one is automatic.

## Automatic: Scanning of the source directory.
1. Markdown files in `sources` will be scanned and automatically indexed, unless marked with the `noindex` frontmatter.
1. This is the fastest method but does not provide as much control.
1. While initially writing your documentation this method is suggested; you can finalize your documentation based on the automatic json file that is produced by this method.
1. The name of the file is important as it contains a pattern to distinguish the chapter/section.  Chapters are not required if all sections are to fit in one chapter.

        {chapter}--{section}.md

## `help.ini`
This is the method that stems from the Drupal advanced help module and looks something like this.  It is explicit, yet gives the lesser control as the input keys are limited.

    [_tasklist]
    title = "My Tasklist"

## `outline.json`
It relies on a json file to provide the outline for your book.  Please refer to `examples/outline.json` for the file schema.

This is the best method for providing exact control as it's completely explicit.  That said, it's tedius to maintain and so the other files below should be understood before you commit to using `outline.json`.  

### `outline.auto.json`
This file is generated during compile IF `outline.json` is not found in the source directory.  It is based on the file structure of `source` plus other meta data (frontmatter, markdown header detection, etc.) as able to be determined during compile.

### `outline.merge.json`
This file will be used during compile to override any values normally visible in `outline.auto.json`.  Use this to override or add to what normally shows up in `outline.auto.json`.  It will have no effect if `outline.json` is present in the `source` directory.
