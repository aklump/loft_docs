# Using API Blueprint

Loft Docs understands API Blueprint if you follow these conventions:

1. In the _source_ directory create _\_apib.twig.md_ with something like the following; this will be the master page for the documentation.  In it, use the token `{{ apib.resources }}` to indicate where the resource markup should appear.

        FORMAT: 1A
        HOST: http://www.my-app.com/api/v1
        
        # My Cool App
        
        This API is a very helpful resource for my users.
        
        {{ apib.resources }}
        
1. Create a folder adjacent to _source_ called _apib_.  This for your resource files.
        
        apib
        ├── companies.apib
        ├── files.apib
        └── persons.apib
        source
        ├── _apib.twig.md
        ├── apib.md (this file is created automatically)
        └── api.md
        
1. In _apib_ create files, e.g. _companies.apib, files.apib, persons.apib_, which represent your API resources following the [API Blueprint specification](https://github.com/apiaryio/api-blueprint/blob/master/API%20Blueprint%20Specification.md).  Refer to [these examples](https://github.com/apiaryio/api-blueprint/tree/master/examples) for more info.  Refer too to _An Example Resource_, below.
1. Lastly create a wrapper page that uses an iframe and place it in _source_, e.g. _api.md_ with at least a title and a reference iframe:

        # API Documentation
        
        [open in new window](apib.html)
        <iframe src="apib.html" height="1200"></iframe>

1. Notice there is a reserved filename _source/apib.md_ which is created automatically, be sure not to create your own version of this file. 
1. When you compile your valid API Blueprint docs will be compiled into your documentation.

## An Example Resource File

<pre>
{% include('_apib_example.md') %}
</pre>
