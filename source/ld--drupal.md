---
title: Drupal Integration
---
[help_module]:http://drupal.org/project/advanced_help
# Drupal Integration
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

## Links
Do not use `&topic:module/topic&` as is suggested, rather follow the instructions above and the Advanced Help topic links will automatically be generated for you.

## Images
Do not use the `&path&` convention when linking to images (as is recommended by the advanced help module), this convention will automatically be added for you during compiling.  Rather, do relative linking like this:

**Correct:**

    <img src="images/my_diagram.png" />

**In-Correct:**

    <img src="&path&images/my_diagram.png" />
