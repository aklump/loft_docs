# Demo: Include Files

The following headline is created during compile by _hooks/pre_compile.php_, which generates a dynamic include file _core/cache/source/_headline.md_ that is inserted here:

{% include('_headline.md') %}

And the following table is defined in a partial in _source/_table.md_.

{% include('_table.md') %}
