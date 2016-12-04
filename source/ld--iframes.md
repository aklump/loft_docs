---
title: Using iFrames
tags: drupal iframe compile
---
### iFrames
One of the cool features is that compiling will grab iframe source and render it directly into the html for offline viewing.  The way to do this is just to include an `iframe` tag in your source code like so:

    <iframe src="http://www.my-site.com/admin/iframe/content" width="100%" height="100%"></iframe>

Then during compiling, the iframe source will be grabbed and then inserted as an html snippet in the place of the `iframe` tag.

#### Behind a Drupal Login
In some cases, your iframe content may be behind a Drupal login.  There is a contingency for this and it involves using the correct settings in `core-config.sh`.  You need to add or uncomment the following, replacing the credentials as appropriate.  That way the compiler will try to log in to your drupal site first before visiting the iframe source url.
    
    credentials = "http://user:pass@www.my-site.com/user/login";
