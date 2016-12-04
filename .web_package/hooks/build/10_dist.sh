#!/bin/bash
#
# @file
# Copy distribution files to /dist
#
sleep 3
test -d "$7/dist" && rm -r "$7/dist"
mkdir -p "$7/dist"
rsync -av "$7/core/" "$7/dist/core/"
cp "$7/core-version.info" "$7/dist/"
rsync -a "$7/public_html/" "$7/docs/"
