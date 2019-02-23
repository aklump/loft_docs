# Using API Blueprint

Loft Docs understands [API Blueprint](https://apiblueprint.org/) when you follow these conventions:

1. [Install Aglio](https://github.com/danielgtaylor/aglio#installation--usage) on your system.
1. Create a hook file and register it in _core-config.sh_; here is an example named _hooks/apib.php_.  Point Loft Docs to your installation of Aglio.  Optionally, pass in some [configuration options](https://github.com/danielgtaylor/aglio#executable).  

        <?php
        
        /**
         * @file
         * Compile the APIBlueprint Snippets.
         *
         * @link https://github.com/danielgtaylor/aglio
         */
        
        $apib
          ->setAglio('/usr/local/bin/aglio', [
            '--theme-full-width',
          ])
          ->compile();


1. In the _source_ directory create _\_apib.twig.md_ with something like the following; this will be the master page for the documentation.  In it, use the token `{{ apib.resources }}` to indicate where the resource markup should appear.

        FORMAT: 1A
        HOST: http://www.my-app.com/api/v1
        
        # My Cool App
        
        This API is a very helpful resource for my users.
        
        {{ apib.resources }}
        
1. Create a folder adjacent to _source/_ called _apib/_.  This for your resource files.
        
        apib
        ├── companies.apib
        ├── files.apib
        └── persons.apib
        source
        ├── _apib.twig.md
        ├── apib.html (this file is created automatically)
        └── api.md
        
1. In _apib/_ create files, e.g. _companies.apib, files.apib, persons.apib_, which represent your API resources following the [API Blueprint specification](https://github.com/apiaryio/api-blueprint/blob/master/API%20Blueprint%20Specification.md).  Refer to [these examples](https://github.com/apiaryio/api-blueprint/tree/master/examples) for more info.  Refer to the code example below: _An Example Resource_.
1. Lastly create a wrapper page that uses an iframe and place it in _source_, e.g. _api.md_ with at least a title and a reference iframe.  You may add other content as desired.

        # API Documentation
        
        <a href="apib.html" target="_blank">open in a new window</a>
        <iframe src="apib.html" height="1200"></iframe>

1. **Do not create _source/apib.md_ nor _source/apib.html_, as these are reserved.**
1. With all of that complete, when you compile, your valid API Blueprint docs will be compiled into your documentation.

## An Example Resource File

<pre>{% include('_apib_example.md') %}</pre>
