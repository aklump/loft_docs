#!/bin/bash
#
# @file
# Configuration

##
 # An array of output formats to disable, if any
 #
#disabled = "website html text mediawiki"

##
 # File path to the php you want to use for compiling
 #
php = $(which php)

##
 # Lynx is required for output of .txt files
 #
lynx = $(which lynx)

##
 # The drupal credentials for a user who can access your iframe content
 #
#credentials = "http://user:pass@www.my-site.com/user/login";

##
 # The name of the drupal module to build advanced help output for, if
 # applicable
 #
drupal_module = 'my_pretend_module';

##
# The location of the advanced help output; this location is used in place of
# the default, if enabled.  It is relative to the directory containing core-config.sh.
#
drupal_dir = 'my_pretend_module/help'

##
 # The file path to an extra README.txt file; when README.md is compiled and
 # this variable is set, the .txt version will be copied to this location.
 #
README = 'README.txt README.md'

##
 # The file path to an extra CHANGELOG.txt file; when CHANGELOG.md is compiled and
 # this variable is set, the .txt version will be copied to this location.
 #
CHANGELOG = 'CHANGELOG.txt'

#
# The path to a .info file or a .json file containing 'version' as a first level key, whose value indicates the documentation version.
# This can be relative to the directory containing core-config.sh or absolute if it begins with a /
version_file = "core-version.info"
