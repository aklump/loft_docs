var tipuesearch = {"pages":[{"title":"Changelog","text":"  0.9.10   POSSIBLE BREAKING CHANGE: Added Deny from All to the .htaccess for public_html pattern.  If this breaks your compile, use a post compile hook to overwrite the default .htaccess file; see hooks\/htaccess.sh for an example of how to do this.  Be sure to register the hook file in core-config.sh in the post_hooks section, or it won't be called.   0.9   Removed support for kit templates. Now using Twig templates. Lost iframe support; plans to bring it back in future.   0.8.18   Removed all traces of doxygene due to no plans for implementation.   0.8.10   API CHANGE: auto-generated.outline.json is now called outline.auto.json   0.8   BREAKING CHANGE: The paths in the config var README are now relative to the directory that contains core-config.sh, not the source folder.  ","tags":"","url":"CHANGELOG.html"},{"title":"What Is Loft Docs?","text":"  Loft Docs is the last project documentation tool you'll ever need.  Loft Docs provides one central place to manage your documentation allowing you to compose in Markdown and have the benefits of simultaneous output to the following formats:   An indexed, multi-page, stand-alone, searchable (via js) website HTML Plaintext MediaWiki Advanced Help for Drupal   Gone are the days of having to update all your different documentation locations!  For installation instructions scroll down.  Features   Tasklist todo item aggregation and sorting. Output to many popular formats. Compilation hooks for before and after. Custom website theming. Searchable pages (in the website output)   Contributing  If you find this project useful... please consider making a donation.  As a Reader   To read documentation you probably just want to load public_html\/index.html in a browser and proceed from there. Plaintext documentation may also be available in text\/. MediaWiki documentation if supported will be found in mediawiki\/.   As an Author   You will concern yourself with the \/source directory, creating your source markdown files here.  This is the source of all documentation. Only files in the source directory should  be edited.  All other files get created during compiling. Images can be added to source\/images. Use relative links when linking to other pages inside source. Use absolute links when linking to anything outside of source.   As an Admin\/Content Manager   You will need to read about compiling below; this is the step needed to generate derivative documentation from \/source.   Linking to Other Help Pages  You should do the following to link internally to source\/page2.html  &lt;a href=\"page2.html\"&gt;Link to Next Page&lt;\/a&gt;   As a Developer  If you are implementing any hooks and you need component or include files, which compile to markdown files in \/source:   Put these component files in \/parts not in \/source. Make sure the generated files begin with the underscore, e.g., _my_compiled_file.md.  That will indicate these files are compiled and can be deleted using core\/clean.   Core update  Loft Docs provides a core update feature as seen below.  From the root directory type:  .\/core\/update   Rationale  The rationalle behind this project is that it is easy to write markdown files, and it is easy to share a static html based documentation file, and it is easy to use Drupal Advanced Help module, and it is easy to version your documentation in git; but to do all this together at once\u2026 was NOT EASY.  But now with Loft Docs... it's easy.  Contact   In the Loft Studios Aaron Klump - Developer PO Box 29294 Bellingham, WA 98228-1294 aim: theloft101 skype: intheloftstudios d.o: aklump http:\/\/www.InTheLoftStudios.com  ","tags":"","url":"README.html"},{"title":"Tasklist","text":"  - [ ] ld--roadmap: Fix the delay caused by deleting files at beginning of compile. - [ ] ld--todos: a task list item - [ ] ld--todos: a task list item @w-10 - [ ] demos--md_extra: Todo items will get aggregated automatically @w10 - [ ] ld--todos: a task list item @w10 - [ ] ld--todos: a task list item @w10.1  ","tags":"","url":"_tasklist.html"},{"title":"Demo: An Example Page &amp; Image","text":"  Section Title  What do you think of my new island?   ","tags":"","url":"demos--example.html"},{"title":"Markdown Extra Demonstration Page","text":"  This page has markdown extra stuff...       do   re       C   D      [ ] Todo items will get aggregated automatically @w10  ","tags":"","url":"demos--md_extra.html"},{"title":"Level One","text":"  Italic text  Bold text  Bold and Italic text  Level Two   do re mi fa   Level Three   uno dos tres quatro   Level Four  What does it do with &lt;?php echo 'this text';?&gt;?  10 print 'hello' 20 goto 10   Level Five  google  Level Six  Freeing Up Harddrive Space  Sites Folder   Use WhatSize appt o locate larger ~Sites\/* Go through and replace older .screenflow movies with their derivative .mov files Delete the contents of the files folders on old sites that are no longer active Go through and zip legacy folders Remove older database snapshots  ","tags":"","url":"demos--mediawiki.html"},{"title":"","text":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla at massa sed nulla consectetur malesuada. Aliquam a sapien non sem rhoncus bibendum quis eu tellus. Nunc luctus fermentum volutpat. Praesent tortor diam, sodales ornare facilisis sit amet, consequat nec elit. Aenean at porttitor purus. Phasellus tempus congue suscipit. Vivamus in magna ante, ut cursus mi. Quisque vel ante in massa pretium condimentum non id risus. Vivamus at felis eu enim egestas feugiat ut sit amet arcu. Nunc eu malesuada nunc. Nam felis lectus, convallis eu commodo eget, vehicula quis sem. Nulla egestas bibendum consequat. Pellentesque tristique lacus at leo dapibus pulvinar. ","tags":"","url":"demos--no-index.html"},{"title":"Subtitle","text":"  This page should not have a title derived from the markdown. ","tags":"","url":"demos--no-title.html"},{"title":"Demo: The Sniffed Title","text":"  This page should not have a title derived from the markdown. ","tags":"","url":"demos--title.html"},{"title":"Auto-compiling","text":"  You can set up an automatic build using the following  .\/core\/watch 10   The argument is the number of seconds to poll.  You can omit and take the default value. ","tags":"","url":"ld--autocompile.html"},{"title":"Compiling","text":"  After a round of changes to the files found in \/source, you will need to export or compile your documentation.  Requirements   Compiling uses Markdown Php, which is included in this distribution. Output of .txt files requires that Lynx be installed.   How to compile  Each time you want to update your documentation files, after modifying files in source you need to execute compile from bash, make sure you are in the root directory of this package.  .\/core\/compile   Folders and Other Includes  By default folders in the source file are copied to the compiled output directories.  In some cases you will want greater control and\/or you will not want to mess up source with boilerplater folders.  For this reason there is a means to include files\/folders in the compiled output by creating defined folder structures and placing your boilerplate content there.  For example to include a js directory in the website output you would create that folder as compile\/public_html\/js.  source\/     about.md compile\/     public_html\/         js\/             app.js             extra.js   If you've reconfigured the folder name for the website directory, make sure to match the folder structure to the reconfigured name.  Defining the documentation version  Some of the templates utilize a version string.  How this is provided is the the next topic covered.  If no version can be found the string will always be 1.0  By default, Loft Docs will look for *.info, in the directory above core\/.  If this is not working or desired then you can specify a path in core-config.sh as such:  version_file = \"\/some\/absolute\/path\/version.info\"   There is a built in a version hook that can sniff a version from .info and .json files and that may suffice.  If not read on about a custom version hook...  A version hook is a php or shell script that echos the version string of your documentation.  These version hook script receives the same arguments as the pre\/post hooks.  You define your version hook in config.  See version_hook.php as an implementation example.  Only one file is allowed in the declaration; either php or shell.  version_hook = \"version_hook.php\"   Removing Compiled Files  You may delete all compiled files using the clean command.  .\/core\/clean  ","tags":"","url":"ld--compiling.html"},{"title":"Drupal Integration","text":"  Compiling will output files compatible with the Advanced Help Module for Drupal.  By default these files will output to a folder named advanced_help, but with a little configuration the folder will output directory to the root of your module folder as help.  This will also output README.txt directly to your module's root directory, so long as you create \/source\/README.md and make the settings shown below.  Follow these steps if you are using this for documenting a Drupal module:   Place this tool in the root of your module directory.  Add it to .gitignore so it doesn't get added to your module's repository. Make sure the folder help does not exist in your module's root; if it does it will be erased! Make sure README.txt does not exist in your module's root; if so it too will be erased during compiling. Make sure the following settings are present in core-config.sh; replace my_module with the name of the module; adjust the paths based on where your source files are in relation to your module's root.  drupal_module = 'my_module'; drupal_dir = '..\/help' README = '..\/..\/README.txt'  Make sure to create \/source\/README.md; this compiles to README.txt. As of version 0.7 the help.ini is automatic.  You may omit it, unless you want to specifically create it. You must also create the advanced help .ini file in source\/help.ini.  If you will be manually creating it read one... Please note that you should omit the module name!  Just name it help.ini.  See this file advanced_help\/help\/using-advanced-help.html inside the Advanced Help Module for more info.   After you've written you markdown files, and compiled, you will see the help directory and the README.txt files in the root of your module.  Links  Do not use &amp;topic:module\/topic&amp; as is suggested, rather follow the instructions above and the Advanced Help topic links will automatically be generated for you.  Images  Do not use the &amp;path&amp; convention when linking to images (as is recommended by the advanced help module), this convention will automatically be added for you during compiling.  Rather, do relative linking like this:  Correct:  &lt;img src=\"images\/my_diagram.png\" \/&gt;   In-Correct:  &lt;img src=\"&amp;path&amp;images\/my_diagram.png\" \/&gt;  ","tags":"","url":"ld--drupal.html"},{"title":"FrontMatter in json","text":"  You can also provide frontmatter to files using outline.merge.json.  Do this when yaml is not appropriate and using json would be easier. ","tags":"code php how-to\nnoindex: true\n---","url":"ld--frontmatter.html"},{"title":"Pre\/Post hooks","text":"  You may specify one or more php or shell scripts to be called both before and\/or after compiling using the pre_hooks and post_hooks config options.  The paths you present are relative to {root}\/hooks.  Compilation pauses until each script is finished.  pre_hooks = \"pre_compile.sh pre_compile.php\" post_hooks = \"post_compile.sh post_compile.php\"   Scripts must be located in {root}\/hooks.  The scripts will receive the following arguments:       php arg   bash arg   description       $argv[1]   $1   The absolute filepath to the source\/ directory     $argv[2]   $2   The absolute filepath to the core\/ directory     $argv[3]   $3   The absolute filepath to the version file     $argv[4]   $4   The absolute filepath to the parent directory of source, a.k.a the root directory     $argv[5]   $5   The absolute filepath to the compiled website directory     $argv[6]   $6   The absolute filepath to the compiled html directory     $argv[7]   $7   The absolute filepath to the compiled text directory     $argv[8]   $8   The absolute filepath to the compiled drupal directory     $argv[9]   $9   The absolute filepath to write dynamic pages before compile     Generating Content  Hooks\/plugins MUST NEVER create files in \/source as this will affect the watcher, instead create any files in $argv[9].  Output  You may print or echo from your script and it will be echoed to the user.  Using Twig Templates for Generated Content  A common pre hook concern is to generate dynamic pages.  If you do this with a php file, you can have access to Twig via the core dependencies.  If your template file is located in source, it should use a .twig extension.  Then in your hook, spit the compiled out as .md.  Here is an example scaffold for a pre hook that uses a twig template to create a page.  hooks\/plugins_dir.php  &lt;?php \/**  * @file Generate the plugins directory page by scanning the plugins directory.  *\/  \/\/ Load the core autoload, which will give access to Twig require_once $argv[2] . '\/vendor\/autoload.php';  \/\/ ... \/\/ Create an array $vars from some process \/\/ ...  $loader = new Twig_Loader_Filesystem(dirname(__FILE__)); $twig = new Twig_Environment($loader);  \/\/ Template file is located in \/hooks as well. $template = $twig-&gt;load('plugins_dir.md');  \/\/ Write the file using $argv[9] to the correct compilation directory. file_put_contents($argv[9] . '\/plugins_dir.md', $template-&gt;render($vars));   hooks\/plugins_dir.md  --- sort: -163 --- # Plugin Library  {% for plugin in plugins %} ## {{ plugin.name }}  &gt; {{ plugin.description }}  {{ plugin.readme }}  ### Usage Example  &lt;pre&gt;{{ plugin.example }}&lt;\/pre&gt;  {% endfor %}  ","tags":"extending pluggable","url":"ld--hooks.html"},{"title":"iFrames","text":"  One of the cool features is that compiling will grab iframe source and render it directly into the html for offline viewing.  The way to do this is just to include an iframe tag in your source code like so:  &lt;iframe src=\"http:\/\/www.my-site.com\/admin\/iframe\/content\" width=\"100%\" height=\"100%\"&gt;&lt;\/iframe&gt;   Then during compiling, the iframe source will be grabbed and then inserted as an html snippet in the place of the iframe tag.  Behind a Drupal Login  In some cases, your iframe content may be behind a Drupal login.  There is a contingency for this and it involves using the correct settings in core-config.sh.  You need to add or uncomment the following, replacing the credentials as appropriate.  That way the compiler will try to log in to your drupal site first before visiting the iframe source url.  credentials = \"http:\/\/user:pass@www.my-site.com\/user\/login\";  ","tags":"drupal iframe compile","url":"ld--iframes.html"},{"title":"Table of Contents\/Indexing","text":"  The index of your documentation may be provided in three ways: two are explicit and one is automatic.  Automatic: Scanning of the source directory.   Markdown files in sources will be scanned and automatically indexed, unless marked with the noindex frontmatter. This is the fastest method but does not provide as much control. While initially writing your documentation this method is suggested; you can finalize your documentation based on the automatic json file that is produced by this method. The name of the file is important as it contains a pattern to distinguish the chapter\/section.  Chapters are not required if all sections are to fit in one chapter.  {chapter}--{section}.md    help.ini  This is the method that stems from the Drupal advanced help module and looks something like this.  It is explicit, yet gives the lesser control as the input keys are limited.  [_tasklist] title = \"My Tasklist\"   outline.json  It relies on a json file to provide the outline for your book.  Please refer to examples\/outline.json for the file schema.  This is the best method for providing exact control as it's completely explicit.  That said, it's tedius to maintain and so the other files below should be understood before you commit to using outline.json.  outline.auto.json  This file is generated during compile IF outline.json is not found in the source directory.  It is based on the file structure of source plus other meta data (frontmatter, markdown header detection, etc.) as able to be determined during compile.  You will find the file at core\/cache\/outline.auto.json.  outline.merge.json  This file will be used during compile to override any values normally visible in outline.auto.json.  Use this to override or add to what normally shows up in outline.auto.json.  It will have no effect if outline.json is present in the source directory. ","tags":"","url":"ld--index.html"},{"title":"Installation","text":"  How you incorporate Loft Docs is up to you, but there are two scenarios which will be described here, with suggested installation instructions.  Quick Start   Copy the contents of \/dist into your project. From within dist, in the shell, run .\/core\/compile. Open dist\/public_html\/welcome.html and follow instructions.   Stand-alone implementation  If your goal is simply to document something, and these files will not integrate into another larger project (think git repo), then this is a stand-alone installation.  This would also be the case where you're using Loft Docs to build a website.  Loft Docs' root is the root of your project.  Here's the minimum file structure of a stand-alone implementation:  \/.gitignore \/core \/core-config.sh \/core-version.info \/public_html \/source \/stand_alone.info   In this scenario the version string of your project is contained in \/stand_alone.info which is one level above Loft Docs' core, and so your config file would contain this line:  version_file = \"..\/web_package.info\"   Or, for greater flexibility (so long as you've only one .info file), it could be:  version_file = \"..\/*.info\"   If you were to host this as a website, public_html is your web root.  Integrated implementation  If you are installing Loft Docs inside the existing code of a larger project, then this constitutes an integrated installation.  Loft Docs is not the root of the larger project, but a sub-folder, maybe you call it docs and store it in the root of the other project.  \/docs\/core \/other_project_file1 \/other_project_file2 \/other_project_file3 \/web_package.info   In this scenario the version string of your project is contained in \/web_package.info which is one levels above Loft Docs, and so your config file would contain this line:  version_file = \"..\/web_package.info\"   Or, for greater flexibility (so long as you've only one .info file), it could be:  version_file = \"..\/*.info\"  ","tags":"","url":"ld--install.html"},{"title":"How to install","text":"   Run the compile command, the first time it is runned, installation takes place.  .\/core\/compile  The necessary dirs will be created including these configuration file(s):  core-config.sh  Open and edit core-config.sh. You should not delete this file once it's been created, as it is the flag that installation has taken place! Compiling without this file may lead to some\/all of your files being deleted. Enter the name of the drupal module this will be used for, if applicable. Enter the credentials for the drupal site if using iframes. Override the php path if needed; php must have the curl library installed. Run .\/core\/compile once more to update the configuration. Test the installation by visiting public_html\/index.html in a browser, this is the webpage output and should show you a few example pages. Installation is complete; you may now begin documenting in source. You most likely should first delete the example files in source.   How to install Lynx on Mac  Here's a quick way to get Lynx on a mac...   Download this application http:\/\/habilis.net\/lynxlet\/ In shell type cd \/usr\/bin Followed by sudo ln -s \/Applications\/Lynxlet.app\/Contents\/Resources\/lynx\/bin\/lynx Test your installation with this command lynx; you should see the lynx browser show up.   or with homebrew   brew install lynx  ","tags":"","url":"ld--installation.html"},{"title":"Roadmap","text":"  Compile time takes too long for videos  When compiling the output directories are deleted.  This needs to change so that if videos are in a directory we don't have to waste time duplicating large files.   [ ] Fix the delay caused by deleting files at beginning of compile.  ","tags":"","url":"ld--roadmap.html"},{"title":"Search","text":"  To enable search you need to create a file in the source directory called:      search--results.md   The file will be used as the stub for the search results page and can either be empty or you can add content to it.  How to explicitly tag content  If you need to add search tags to pages , use the front matter with the key: tags.  Follow these guidlines:   Tags MUST NOT contain a space.  --- tags: code php how-to ---    How to exclude a page from the search index  There are two ways.  For markdown source files, front matter is preferred.   Use the frontmatter like this:  --- search: noindex ---    When that can't be used, say for an .html file, you can use the outline.merge.json file with the  {     \"frontmatter\": [         {             \"master.apib.html\": {                 \"search\": \"noindex\"             }         }     ] }  ","tags":"","url":"ld--search.html"},{"title":"Custom Theming the Website Version","text":"  The files in \/core\/tpl control the output of the .html files found in the website folder public_html.  You should never modify these files, nor any files in core.  Instead to override the theming you should copy core\/tpl up one directory into the base directory and override those files.  cp -R core\/tpl .   For css changes you should edit \/tpl\/style.css in the newly created \/tpl folder. ","tags":"","url":"ld--theming.html"},{"title":"Todo Items\/Tasklist","text":"  Todo item aggregation is a neat feature that works thus.  Add one or more todo items to your markdown source files using the following syntax and when you compile, your todo items will be aggregated and sorted into a new markdown file called _tasklist.md.  Do not alter the content of _tasklist.md or you will loose your work.  If you want this to show up in indexing, make sure to add it to help.ini.  If you're instead using the json index outline format, then it will automatically show up unless you remove the following from outline.merge.json and outline.json from the sections:      {         \"id\": \"_tasklist\",         \"file\": \"_tasklist.md\",         \"title\": \"All Todo Items\",         \"sort\": 1000     }   When items are aggregated, the filenames are prepended to the todo item.  The final list will be filtered for uniqueness, and duplicates removed.  If the same todo item appears more than once in a file, it will be reduced to a single item; but the same todo item can appear in more than one file, since the filename prepend creates uniqueness.  - [ ] a task list item   Sorting todo items  Use the weight flag @w followed by an int or float number indicating the sort weight.  Lower numbers appear first.  Sorting will happen only on the aggregated tasklist, not in the indiviual source files.  - [ ] a task list item @w-10 - [ ] a task list item @w10 - [ ] a task list item @w10.1  ","tags":"","url":"ld--todos.html"},{"title":"Integrate with Visual Sitemap","text":"  To include a sitemap as generated by http:\/\/www.intheloftstudios.com\/packages\/development\/visual_sitemap.   Create a hook file called sitemap.sh with the following:  #!\/usr\/bin\/env bash  docs_vismap=$(type vismap &gt;\/dev\/null &amp;2&gt;&amp;1 &amp;&amp; which vismap)  if [ \"$docs_vismap\" ]; then     cd \"$1\"     $docs_vismap sitemap.json --out=\"$9\/vismap.html\" -f fi  Add the filename to core-config.sh Create your sitemap json in source\/sitemap.json. Add a file called sitemap.md with something like the following; the iframe is the important part.  # Visual Sitemap  &lt;iframe src=\"vismap.html\" height=\"1200\"&gt;&lt;\/iframe&gt;   ","tags":"","url":"ld--visual-sitemap.html"},{"title":"Search Results","text":" ","tags":"","url":"search--results.html"}]};
