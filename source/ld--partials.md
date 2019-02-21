# Partials (Include Files)

* Files in _source_, which begin with an underscore are considered partials and will be ignored during render, e.g., _\_table.md_
* Include such files using the twig syntax, `{% include('_table.md') %}`
* Name these parent files ending with `.twig.md` so the Twig processor runs on them.

## An Example

> **Goal:** To create a file that defines a table and include that table in two different pages.

Your _source_ directory will resemble the following:

    .
    ├── _table.md
    ├── page.twig.md
    └── page2.twig.md

The contents of _\_table.md_:

    | Key | Value |
    |----------|----------|
    | Size | large  |
    | Shape | square |

The contents of _page.twig.md_:

    # Page One
    
    Here is the table:
    
    {% include('_table.md') %}
    
The contents of _page2.twig.md_:

    # Page Two
    
    And again a reference to the table:
    
    {% include('_table.md') %}    

## Creating Partials During Compile

Using a PHP hook file, you can generate dynamic partials during compile.  These can be referenced in your static source files just like other partials.  Here's an example of code for a hook file that generates a dynamic partial called _\_headline.md_.  Dynamic files are automatically cleaned up by the compiler.

    <?php
    $contents = "## Today is: " . date('r');
    echo $compiler->addInclude('_headline.md', $contents)
        ->getBasename() . ' has been created.' && exit(0);
    exit(1);
