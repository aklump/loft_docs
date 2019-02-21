# Twig

You may want to dynamically create partials that twig can load during compile.

In a hook file create a partial and save it to:


Any file that you name with ending in `.twig.md` will be run through the twig processor.  That means that you can use includes like the following:

    {% include('my_dynamic_file.md') %}
    
