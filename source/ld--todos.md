---
title: The TODO list feature
---
# Todo Items/Tasklist
Todo item aggregation is a neat feature that works thus.  Add one or more todo items to your markdown source files using the [following syntax](https://github.com/blog/1375-task-lists-in-gfm-issues-pulls-comments) and when you compile, your todo items will be aggregated and sorted into a new markdown file called `_tasklist.md`.  **Do not alter the content of `_tasklist.md` or you will loose your work.**

If you want this to show up in indexing, make sure to add it to _help.ini_.
  
If you're instead using the json index outline format, then it will automatically show up unless you remove the following from `outline.merge.json` and `outline.json` from the `sections`:

        {
            "id": "_tasklist",
            "file": "_tasklist.md",
            "title": "All Todo Items",
        }

When items are aggregated, the filenames are prepended to the todo item.  The final list will be filtered for uniqueness, and duplicates removed.  If the same todo item appears more than once in a file, it will be reduced to a single item; but the same todo item can appear in more than one file, since the filename prepend creates uniqueness.

    - [ ] a task list item

## Sorting todo items
Use the weight flag `@w` followed by an int or float number indicating the sort weight.  Lower numbers appear first.  Sorting will happen only on the aggregated tasklist, not in the indiviual source files.

    - [ ] a task list item @w-10
    - [ ] a task list item @w10
    - [ ] a task list item @w10.1
