<?php
/**
 * @file
 * An example of a pre-compile php file
 *
 * @ingroup loft_docs
 * @{
 */

// Add vars to the twig render for testing addVariables().
$compiler->addVariables([
  'apib' => ['resources' => '{{ apib.resources }}'],
  'lorem' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent ac blandit risus. Mauris tempor a lacus a placerat. Vivamus viverra dapibus metus non finibus. Nulla ultricies est nulla, eget efficitur nibh viverra non. Sed sed est viverra nunc malesuada venenatis vitae at tellus. Suspendisse potenti. Morbi non blandit elit, sit amet consectetur mi.

Fusce vel sapien quis orci feugiat accumsan vel sit amet massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nunc varius turpis vel placerat eleifend. Vivamus tempus enim quam, sit amet porta libero efficitur ac. Maecenas ultricies, felis id vulputate consectetur, ligula ligula tempor augue, et feugiat sapien ante sit amet dui. Morbi ullamcorper justo nec purus cursus ullamcorper. Sed semper dictum tellus, vel varius metus pellentesque eu. Ut interdum tristique finibus. In pharetra nibh a malesuada dignissim. Etiam a interdum orci. Maecenas ultricies porttitor neque. Quisque sit amet tincidunt nulla, ut aliquam mauris. Pellentesque ut efficitur eros. Aenean vestibulum aliquet odio, quis pellentesque mauris congue id. Aenean vitae turpis id sapien sollicitudin blandit.

Praesent at vulputate tellus, vehicula dignissim elit. Interdum et malesuada fames ac ante ipsum primis in faucibus. In molestie est sapien, sit amet porttitor ipsum laoreet sit amet. Praesent lacus neque, suscipit quis urna non, varius consequat justo. Curabitur tincidunt leo ac venenatis pharetra. Curabitur ultricies odio ut nibh viverra lobortis. Sed scelerisque pellentesque vehicula.',
]);

// Pull an example from GitHub for our apib docs.
$contents = file_get_contents('https://raw.githubusercontent.com/apiaryio/api-blueprint/master/examples/Real%20World%20API.md');
$compiler->addInclude('_apib_example.md', $contents);

// Create an include file for testing addInclude().
$contents = "## Today is: " . date('r');
$compiler->addInclude('_headline.md', $contents);

$compiler->addSourceFile('demos--dynamic-source-file.md', "# Dynamic Source File\n\nThis file was created in " . __FILE__);

