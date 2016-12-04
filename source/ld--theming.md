# Custom Theming the Website Version
The files in `/core/tpl` control the output of the `.html` files found in the website folder `public_html`.  You should never modify these files, nor any files in `core`.  Instead to override the theming you should copy `core/tpl` up one directory into the base directory and override those files.

    cp -R core/tpl .
    
For css changes you should edit `/tpl/style.css` in the newly created `/tpl` folder.
    
