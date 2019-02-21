# Tasklist (Todo Items)

* You must enable tasklist aggregation in _outline.merge.json_.
* A partial will be created of todo items called _\_tasklist.md_.  You may include it like other include files.
* Add one or more todo items to your markdown source files using the [following syntax](https://github.com/blog/1375-task-lists-in-gfm-issues-pulls-comments).
* When items are aggregated, the filenames are prepended to the todo item.  The final list will be filtered for uniqueness, and duplicates removed.  If the same todo item appears more than once in a file, it will be reduced to a single item; but the same todo item can appear in more than one file, since the filename prepend creates uniqueness.

## How to Enable

The following must appear in a _source/outline.merge.json_ to enable todo aggregation.

    {
        "settings": {
            "tasklist": {
                "aggregate": true
            }
        }
    }


## An Example File With Tasks

    # A Page With Tasks
    
    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
    
    - [ ] a task list item
    - [ ] another task
    
    Praesent at vulputate tellus, vehicula dignissim elit.

## Including the Tasklist on Another Page

You can do something like the following in a _.twig.md_ file, and the aggregated tasklist will appear there.

    # Roadmap
    
    Here are the todo items left for this project.
    
    {% include('_tasklist.md') %}

## Sorting Tasks in the Final List

You can control the order the tasks appear **in the aggregated list** by using the weight flag `@w` followed by an int or float number indicating the sort weight.  Lower numbers appear first.  The source file orders are not changed by the use of `@w` flag.

    - [ ] a task list item @w-10
    - [ ] a task list item @w10
    - [ ] a task list item @w10.1
