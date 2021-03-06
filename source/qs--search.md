---
title: Integrating search
---
# Search

To enable search you need to create a file in the source directory called _search--results.md_.  This file will be used as the stub for the search results page and can either be empty or you can add content to it.

## How to explicitly tag content

If you need to add search tags to pages , use the frontmatter with the key: _tags_.  Follow these guidlines:

1. Tags MUST NOT contain a space.

        ---
        tags: code php how-to
        ---

## How to exclude a page from the search index

There are two ways.  For markdown source files, frontmatter is preferred.

1. Use the frontmatter like this:

        ---
        search: noindex
        ---

When that can't be used, say for an `.html` file, you can use the `outline.merge.json` file with the 

    {
        "frontmatter": [
            {
                "master.apib.html": {
                    "search": "noindex"
                }
            }
        ]
    }
