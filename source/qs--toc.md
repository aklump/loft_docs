---
tags: needs_work
---
# Table of Contents

The table of contents is generated automatically based on the filenames of your markdown files.  The pages are listed alphabetically based on the page titles.  To overide this automation you will use a file called _outline.merge.json_ which is also placed in the _source_ directory; please refer to `examples/outline.json` for the complete file schema.

## To Change the Order

You can control the order of pages listed in the TOC by adding to _outline.merge.json_.  The order of items added to the `sections` key takes precedence.  All that is needed to affect order is the `id`.  In the following code snippet you see that _installation_ will appear first in the index, followed by _changelog_.  The remaining files will appear in the default title ascending order.  Notice that in the second element the `title` key is also used in _outline.merge.json_.  This allows you to control the _index title_ for a page, independant of the how the title is displayed when the page is viewed.

    {
        "sections": [
            {
                "id": "installation"
            },
            {
                "id": "changelog",
                "title": "CHANGELOG"
            }
        ],
        ...

## Using Chapters

It's often nice to subdivide your documentation into chapters and this is very easy to do.  It requires two additional steps:

1. Prepend files with a chapter `<chapter_id>--`, e.g. _qs--installation.md_ and _adv--files.md_.
1. Define the chapters in _outline.merge.json_.

        {
            "chapters": [
                {
                    "id": "qs",
                    "title": "Quick Start"
                },
                {
                    "id": "adv",
                    "title": "Advanced Usage"
                }
            ],
        ....            

## Skipping Index of Some Files

To cause the automated indexing to skip over a page, either make that page a partial, e.g. _\_fragment.md_, or set the `noindex` frontmatter on the file.  See the frontmatter section for more info.

## _help.ini_

This is the method that stems from the [Drupal advanced help module](https://www.drupal.org/project/advanced_help) and looks something like the following.  Using _outline.merge.json_ is probably the better approach, and this will most likely be deprecated in future versions.

    [_tasklist]
    title = "My Tasklist"
