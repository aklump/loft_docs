# Using Variables

To provide variables that are used in _.twig.md_ files you can either provide global variables in _source/outline.merge.json_ as `variables`, or you can generate them in a pre hook.

## Global Static Variables

1. Add something like the following to  _source/outline.merge.json_:

        {
            "variables": {
                "website_url": "https://www.my-website.com",
                "author": "Aaron Klump"
            }
        }

## Dynamic During Compile

In a PHP pre hook file do something like:

    $compiler->addVariables(['key' => 'value']);

## Printing Variable Values

1. Enable Twig rendering on the page where you want to use the variables by adding the file suffix _.twig.md_.
1. Use Twig replacement syntax, e.g. `{{ author }}`.

