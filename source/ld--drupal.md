---
title: Drupal Integration
---
[help_module]:http://drupal.org/project/advanced_help
# Drupal Integration

## Advanced Help Module

Compiling will output files compatible with the [Advanced Help Module for Drupal][help_module].  By default these files will output to a folder named `advanced_help`, but with a little configuration the folder will output directory to the root of your module folder as `help`.

This will also output `README.txt` directly to your module's root directory, so long as you create `/source/README.md` and make the settings shown below.

Follow these steps if you are using this for documenting a Drupal module:

1. Place this tool in the root of your module directory.  Add it to `.gitignore` so it doesn't get added to your module's repository.
3. Make sure the folder `help` does not exist in your module's root; if it does it will be erased!
4. Make sure `README.txt` does not exist in your module's root; if so it too will be erased during compiling.
1. Make sure the following settings are present in `core-config.sh`; replace `my_module` with the name of the module; adjust the paths based on where your source files are in relation to your module's root.
        
        drupal_module = 'my_module';
        drupal_dir = '../help'
        README = '../../README.txt'
        
2. Make sure to create `/source/README.md`; this compiles to `README.txt`.
3. As of version 0.7 the `help.ini` is automatic.  You may omit it, unless you want to specifically create it. <s>You must also create the advanced help .ini file in `source/help.ini`.</s>  If you will be manually creating it read one... **Please note that you should omit the module name!**  Just name it `help.ini`.  See this file `advanced_help/help/using-advanced-help.html` inside the [Advanced Help Module][help_module] for more info.

After you've written you markdown files, and compiled, you will see the `help` directory and the `README.txt` files in the root of your module.

### Links
Do not use `&topic:module/topic&` as is suggested, rather follow the instructions above and the Advanced Help topic links will automatically be generated for you.

### Images
Do not use the `&path&` convention when linking to images (as is recommended by the advanced help module), this convention will automatically be added for you during compiling.  Rather, do relative linking like this:

**Correct:**

    <img src="images/my_diagram.png" />

**In-Correct:**

    <img src="&path&images/my_diagram.png" />

## Project Page Support

If you are managing a project page at Drupal.org, you can try this strategy to keep your project page in sync with your _README.txt_ file.

    .
    ├── README.twig.md
    ├── _overview.md
    └── drupal_project_page.twig.md

1. In _\_overview.md_ put the shared content; that which should appear on the project page and in the README file.
1. Use an include `{% include('_overview.md') %}` in both files to pull in the shared info.
1. Make sure `html` is not disabled in the configuration.

        disabled = "text mediawiki"
        
1. Make sure README is configured, as well.
    
        README = '../README.txt'
    
1. Compile docs.
1. Copy the contents of _html/drupal_project_page.html_ into the project page at drupal.org.
