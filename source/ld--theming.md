---
id: theming
---
# Custom Theming the Website Version

The files in `/core/plugins/twig/tpl .` control the output of the `.html` files found in the website folder `public_html`.  You should never modify these files, nor any files in `core`.  Instead to override the theming you should copy and modify that directory, e.g.

    cp -R core/plugins/twig/tpl .
    
For CSS changes you should edit `/tpl/style.css` in the newly created `/tpl` folder.  Or the SASS files as desired.
