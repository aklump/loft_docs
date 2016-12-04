---
title: Frontmatter and How to Use
---
As of version 0.8 YAML front matter is supported in source files.  This is used to define items such as:

| key | description |
|----------|----------|
| title | The title as it appears in the index |
| sort | The order the page appears in the index and compilations |
| chapter | The id of the chapter; not the chapter title, mind you. |
| tags | Search tags, space separated |
| noindex | Set this to true and the file will not be added to the index automatically |

Read more about [Front Matter](http://assemble.io/docs/YAML-front-matter.html)


Here is an example:

    ---
    title: The title of this page
    sort: -100
    tags: code php how-to
    noindex: true
    ---
