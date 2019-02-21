---
title: Frontmatter and How to Use
---
As of version 0.8 YAML front matter is supported in source files.  This is used to define items such as:

| key | description |
|----------|----------|
| title | The title as it appears in the index |
| chapter | The id of the chapter; not the chapter title, mind you. |
| tags | Search tags, space separated |
| noindex | Set this to true to ignore or exclude a file from the index |
| search | Set this to "noindex" to prevent search indexing on this file |
| tokens | An array of find/replace tokens that will processed before compile |
| twig | Like `tokens` except that the find is expecting twig-syntax {{ find }}.  Saves you from having to include the curly braces in your front matter |

Read more about [Front Matter](http://assemble.io/docs/YAML-front-matter.html)


Here is an example:

    ---
    title: The title of this page
    tags: code php how-to
    noindex: true
    ---

## FrontMatter in json

You can also provide frontmatter to files using outline.merge.json.  Do this when yaml is not appropriate and using json would be easier.
