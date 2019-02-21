# Generating Cheatsheets

Loft Docs makes it easy to generate cheatsheets based on a class's methods.  The output might look something like this:

| BrowserTestCase |
|---|
| <strong>assert</strong> <em>($fail_message = '')</em> |
| <strong>assertElementExists</strong> <em>($css_selector, $failure_message = '')</em> |
| <strong>assertElementNotExists</strong> <em>($css_selector, $failure_message = '')</em> |
| <strong>assertElementNotVisible</strong> <em>($css_selector, $failure_message = '')</em> |
| <strong>assertElementVisible</strong> <em>($css_selector, $failure_message = '')</em> |
| <strong>el</strong> <em>($css_selector)</em> |
| <strong>els</strong> <em>($css_selector)</em> |
| <strong>generate</strong> <em>($method)</em> |
| <strong>getDomElements</strong> <em>(array $css_selectors)</em> |
| <strong>getSession</strong> <em>()</em> |
| <strong>getStored</strong> <em>($key, $default = NULL)</em> |
| <strong>handleBaseUrl</strong> <em>()</em> |
| <strong>loadPageByUrl</strong> <em>($url)</em> |
| <strong>resolveUrl</strong> <em>($url, $remove_authentication_credentials = false)</em> |
| <strong>store</strong> <em>($key, $value)</em> |


## The Hook File Example Code
  
  Here's an example hook file that generated the above; be sure to register the hook in the config file.

    <?php
    
    /**
     * @file
     * An hook example of generating a PHP class method cheatsheet.
     */
    
    use AKlump\LoftDocs\PhpClassMethodReader;
    use AKlump\LoftLib\Code\Markdown;
    use AKlump\LoftLib\Storage\FilePath;
    
    // Define the classes to read.
    $reader = new PhpClassMethodReader();
    
    // The goal is to create a cheatsheet of methods in \AKlump\DrupalTest\BrowserTestCase.
    $reader->addClassToScan('\AKlump\DrupalTest\BrowserTestCase', [
    
      // But we want to exclude the method called 'getBrowser', so we use the
      // second parameter which defines a filter.
      PhpClassMethodReader::EXCLUDE,
      ['/^(getBrowser)$/'],
    ]);
    
    // Convert the scanned data into a markup table for each group, in this
    // example there is only one group, because we are using only one class.
    foreach ($reader->scan() as $group => $methods) {
      $contents = '';
      $methods = array_map(function ($method) use ($group) {
        return [$group => '<strong>' . $method['name'] . '</strong> <em>(' . implode(', ', $method['params']) . ')</em>'];
      }, $methods);
      $contents .= Markdown::table($methods) . PHP_EOL;
    
      // Save the snippet to be used by other pages.
      FilePath::create($argv[9] . "/_$group.md")->put($contents)->save();
    }



