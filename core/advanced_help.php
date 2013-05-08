<?php
/**
 * @file
 * Process advanced help html files to make them fit into said format
 *
 * @ingroup ovadata_docs
 * @{
 */

//Inside your help file, link to other topics using the format <a
//href="&topic:module/topic&">. This format will ensure the popup status remains
//consistent when switching between links.
//
//Use <a href="&path&example.jpg"> to reference items within the help directory,
//such as images you wish to embed within the help text.
//
//Use <a href="&base_url&admin/settings/site-configuration"> to reference any
//normal path in the site.


// Convert paths to images to include @page
if (isset($argv[1])
    && ($output = file_get_contents(getcwd() . '/' . $argv[1]))) {

  // Replace images
  if (preg_match_all('/<img\s+src="([^"]+)".*?>/', $output, $images)) {
    foreach (array_keys($images[0]) as $key) {
      $output = str_replace($images[1][$key], '&path&' . $images[1][$key], $output);
    }
  }

  // Replace links to other help topic pages
  if (preg_match_all('/<a\s+href="(?!http:\/\/)(?!\/)([^"]+)".*?>/', $output, $links)) {
    foreach (array_keys($links[0]) as $key) {
      $info = pathinfo($links[1][$key]);
      $output = str_replace($links[1][$key], '&topic:' . $argv[2] . '/' . $info['filename'] . '&' , $output);
    }
  }
}
print $output;

/** @} */ //end of group: ovadata_docs
