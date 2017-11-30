#!/bin/bash
#
# @file
# Copy distribution files to /dist
#

# Allow time for all CodeKit to compile.
sleep 3

# First, wipe out the dist folder for a clean slate.
cd "$7" && (! test -e dist || rm -r dist) && mkdir dist

# Now copy of the necessary folders; don't check first because we want a loud failure.
rsync -a "$7/core/" "$7/dist/core/"
rsync -a "$7/public_html/" "$7/dist/docs/"

# ... and files.
cp "$7/core-version.info" "$7/dist/"
cp "$7/CHANGELOG.txt" "$7/dist/"
cp "$7/README.txt" "$7/dist/"
cp "$7/gitignore" "$7/dist/"
