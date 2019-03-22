# Using Variables

To provide variables that are used in _.twig.md_ files you can either provide static variables in a file _source/_variables.json_, or you can generate them in a pre hook.

## Dynamic During Compile

In a PHP pre hook file do something like:

    $compiler->addVariables(['key' => 'value']);

## Static File

1. Create a file _source/_variables.json_ with something like this:

        {
            "author": "Aaron Klump"
        }

## Using Variables

1. Name the page where you want to use the variables with the suffix _.twig.md_.
1. Use Twig replacement, e.g. `{{ author }}`.

