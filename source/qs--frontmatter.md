---
id: frontmatter
title: Frontmatter and How to Use
---
As of version 0.8 YAML frontmatter is supported in source files.  This is used to define items such as:

| key | description |
|----------|----------|
| title | The title as it appears in the index |
| chapter | The id of the chapter; not the chapter title, mind you. |
| tags | Search tags, space separated |
| noindex | Set this to true to ignore or exclude a file from the index |
| search | Set this to "noindex" to prevent search indexing on this file |
| tokens | An array of find/replace tokens that will processed before compile |
| twig | Like `tokens` except that the find is expecting twig-syntax {{ find }}.  Saves you from having to include the curly braces in your frontmatter |

Read more about [frontmatter](http://assemble.io/docs/YAML-front-matter.html) on the web.

Here is an example:

```
---
title: The title of this page
tags: code php how-to
noindex: true
---
```

## Frontmatter as an HTML Comment

You may use an HTML comment syntax instead at the top of your markdown file, if preferred.  Inside the comment the format is still YAML.

```html
<!--
title: The title of this page
tags: code php how-to
noindex: true
-->
```

## Metadata as JSON

You can also provide file metadata using _outline.merge.json_.  Do this when yaml is not appropriate and using JSON would be easier.  Here is an example:

    {
        "frontmatter": {
            "demos--search-noindex2.html": {
                "search": "noindex"
            }
        }
    }

## Frontmatter as Tokens

Frontmatter is available **when using the Twig extension** `.twig.md` and preceding the keys with `meta`.  For example:

In _source/quote.twig.md_...
````
---
written: 2021-09-18
---
"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."

Author, {{ meta.written|date("F j, Y") }}
```

